<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\Folder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FolderController extends AbstractController
{
    private string $uploadDir;

    public function __construct() {
        // $this->uploadDir = __DIR__ . '/../../../../public/uploads';
        // if (!is_dir($this->uploadDir)) mkdir($this->uploadDir, 0777, true);
    }

    #[Route(path: '/api/folders', name: 'api.folder')]
    public function index()
    {
        return $this->render('folder/index.html.twig', [
        ]);
    }

    // --- FOLDERS ---
    #[Route('/folders', name: 'folders_list', methods: ['GET'])]
    public function listFolders(EntityManagerInterface $em): Response {
        $folders = $em->getRepository(Folder::class)->findAll();
        $data = array_map(fn($f) => ['id'=>$f->getId(), 'name'=>$f->getName()], $folders);
        return $this->json($data);
    }

    #[Route('/folders', name: 'folders_create', methods: ['POST'])]
    public function createFolder(Request $request, EntityManagerInterface $em): Response {
        $content = json_decode($request->getContent(), true);
        if (!isset($content['name']) || empty($content['name'])) {
            return $this->json(['error'=>'Name required'], 400);
        }
        $folder = new Folder();
        $folder->setName($content['name']);
        $em->persist($folder);
        $em->flush();

        // crée le dossier physique
        $folderPath = $this->uploadDir.'/'.$folder->getId();
        mkdir($folderPath);

        return $this->json(['id'=>$folder->getId(), 'name'=>$folder->getName()], 201);
    }

    #[Route('/folders/{id}', name: 'folders_delete', methods: ['DELETE'])]
    public function deleteFolder(Folder $folder, EntityManagerInterface $em): Response {
        $folderPath = $this->uploadDir.'/'.$folder->getId();
        if (is_dir($folderPath)) {
            $fs = new Filesystem();
            $fs->remove($folderPath);
        }
        $em->remove($folder);
        $em->flush();

        return $this->json(['message'=>'Folder deleted']);
    }

    // --- FILES ---
    #[Route('/files', name: 'files_list', methods: ['GET'])]
    public function listFiles(EntityManagerInterface $em): Response {
        $files = $em->getRepository(File::class)->findAll();
        $data = array_map(fn($f) => [
            'id'=>$f->getId(),
            'folder_id'=>$f->getFolder()->getId(),
            'filename'=>$f->getFilename()
        ], $files);
        return $this->json($data);
    }

    #[Route('/files', name: 'files_create', methods: ['POST'])]
    public function uploadFile(Request $request, EntityManagerInterface $em): Response {
        $folderId = $request->request->get('folder_id');
        $uploadedFile = $request->files->get('file');

        if (!$folderId || !$uploadedFile) {
            return $this->json(['error'=>'folder_id and file required'], 400);
        }

        $folder = $em->getRepository(Folder::class)->find($folderId);
        if (!$folder) return $this->json(['error'=>'Folder not found'], 404);

        $filename = uniqid().'_'.$uploadedFile->getClientOriginalName();
        $uploadedFile->move($this->uploadDir.'/'.$folder->getId(), $filename);

        $fileEntity = new File();
        $fileEntity->setFilename($filename);
        $fileEntity->setFolder($folder);

        $em->persist($fileEntity);
        $em->flush();

        return $this->json(['message'=>'File uploaded', 'filename'=>$filename], 201);
    }

    #[Route('/files/{id}', name: 'files_delete', methods: ['DELETE'])]
    public function deleteFile(File $file, EntityManagerInterface $em): Response {
        $filePath = $this->uploadDir.'/'.$file->getFolder()->getId().'/'.$file->getFilename();
        if (file_exists($filePath)) unlink($filePath);

        $em->remove($file);
        $em->flush();

        return $this->json(['message'=>'File deleted']);
    }

}

/*
  * `GET /api/folders`
  * `POST /api/folders`
  * `DELETE /api/folders/{id}`
  * `GET /api/files`
  * `POST /api/files`
  * `DELETE /api/files/{id}`
*/