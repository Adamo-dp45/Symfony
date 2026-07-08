import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import { resolve } from 'node:path'

/**
 * Permet de rafraichir la page quand on modifie un fichier twig
 */
const twigRefreshPlugin = {
    name: 'twig-refresh',
    configureServer({ watcher, ws }) { // Permet de modifier le serveur de developpement de vite
        watcher.add(resolve('templates/**/*.twig')) // Ou 'resolve(__dirname, "templates/**/*.twig")'
        watcher.on('change', function(path) {
            if(path.endsWith('.twig')) {
                ws.send({
                    type: 'full-reload'
                })
            }
        })
    }
}

// https://vite.dev/config/
export default defineConfig({
    plugins: [react(), twigRefreshPlugin],
    server: {
        watch: {
            disableGlobbing: false // Pour qu'il comprenne le globbing '**/*'
        }
    },

    root: './assets', // La racine du projet
    base: '/assets', // Le chemin de base vers les assets
    build: { // Pour la production
        outDir: '../public/assets', // Le dossier de destination mais est relation à la racine du 'root'
        /*
            - Si on fais un build à ce niveau on aura l'erreur 'Could not resolve entry module "assets/index.html"' car par défaut il utilise 'index.html' pour connaître les points d'entrées donc 'rollupOptions'
        */
        manifest: true, // Permet de générer un fichier 'manifest.json' qui va contenir les informations sur le build
        assetsDir: '', // Pour éviter de créer un sous dossier dans le dossier de build
        rollupOptions: {
            input: {
                'main.jsx': './assets/main.jsx'
            },
            output: {
                manualChunks: undefined // Permet de désactiver la séparation du 'vendor' si on compte n'avoir qu'un seule fichier d'entrée
            }
        },
        // sourcemap: true -- Les fichiers de sources

        /*
            - Lors du build on aura ce warning '(!) outDir C:\Users\ADAMO\Documents\Web\Symfony\public\assets is not inside project root and will not be emptied. Use --emptyOutDir to override.' qui signifie que 'assets' n'est pas dans le projet car par défaut tous ce qui n'est pas dans le 'root' il ne le nettoie pas, si on n'est sur de nous on peut ajouter '--emptyOutDir' qui va vider le dossier de destination à chaque fois
            - Dans le cas d'un warning de 'base..' on ajoute dans 'base' un slash à la fin '/assets/'
        */
    }
})
