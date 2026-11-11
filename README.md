### Symfony `symfony.com`

- php -S localhost:8000 -t public | symfony serve
- php bin/phpunit
- npm run dev-server

- **Command**
    > php bin/console list : Permet de lister l'ensemble des opérations qu'on peut faire
    > php bin/console make : !! toutes les commandes disponible via `make`
    > php bin/console cache:clear : !! nettoyer le cache
    > php bin/console cache:warmup : !! reconstruire le cache sans le vidé, utiliser après des changements de configuration..
    > php bin/console help make:entity : !! d'afficher l'aide d'une commande
    > php bin/console about : !! vérifier l'état et donne les informations sur notre application
    > php bin/console debug:router : !! voir l'ensemble des routes dans notre application
    > php bin/console debug:autowiring : !! les class ou interface qu'on peut injecter dynamiquement
    > php bin/console debug:autowiring Password : Permet de chercher un service qu'on pourra ensuite injecté, `valid` pour les validateurs, `mail` pour l'envoi d'email
    > php bin/console debug:container --parameters : !! montrer tous les services qui sont dans les container du genre chemin de dossier ex: `kernel.project_dir` chemin du dossier `public`, `| grep dir` pour voir ceux qui sert de chemin
    > php bin/console security:hash-password : Pour avoir le hash d'un mot de passe
    > php bin/console make:serializer:encoder : !! créer un encoder

- La variable globle `app` permet de récupérer plusieurs données dans la vue
    > `app.user` pour récupérer l'objet utilisateur actuel ou null si il n'est pas authentifié
    > `app.request` !! l'objet `Request` qui stocke les données de la demande en cours
    > `app.session` !! l'objet session qui est la session de l'utilisateur actuel ou null
    > `app.flashes` !! le tableau de tous les messages flash stokés dans la session
    > `app.environment` !! le nom de l'environnement de configuration actuel
    > `app.debug` pour voir l'état du mode debug
    > `app.token` !! l'objet `TokenInterface` qui représente le jeton de sécurité
    > `app.current_route` !! le nom de l'itinéraire associé à la requête en cours ou null
    > `app.current_route_parametres` !!
    > `app.locale` !! la locale
    > `app.enabled_locale` !!

- **Important !**
    > Par défaut quand on retourne un `JsonResponse` dans un controller il va mettre automatiquement les bonnes en tête
    > Pour utiliser une variable d'env dans un controller, dans `config/services.yaml` - `parameters:` - `app.name: '%env(APP_NAME)%'` puis injecté `ParameterBagInterface` - `$params->get('app.name')` ou utiliser l'attribut #[Autowire('%app.name%')]
    > L'url `symfony.com/bundles` permet de voir les bundles supportés par symfony
    > Le package `Symfony Vite Pentatrion` permet d'installer symfony avec `vite` intégrer

- **Controller**
    > php bin/console make:controller PostController : Pour créer un controller
    > php bin/console make:crud Recipe : !! un crud avec une entité

- **Entité**
    > php bin/console make:entity PostEntity : Permet de créer une entité
        > Une convention pour les dates `createdAt` ou `updateAt`

- **Twig** - Le moteur de template `index.html.twig`
    > Le moteur twig dispose des choses comme les fonctions, filtre, extension, tag, macro, global, test, operator
        > Dans `symfony` les extensions sont rajoutés automatiquement
    > Les bundles
        > composer require twig/extra-bundle : Un bundle regroupant toutes les extensions extra de twig, sans nécessiter de configuration manuelle lors de l'installation individuelle des extensions, regroupe `string-extra`, `html-extra`, `markdown-extra`, `intl-extra`, `cssinliner-extra`..
            > composer require twig/string-extra : Pour manipuler des chaînes de caractère
            > composer require twig/markdown-extra : !! ajouter le support de la syntaxe markdown
                > composer require league/commonmark : !! utiliser le filtre `markdown_to_html`
            > composer require twig/html-extra : !!
    > Le `cache` twig qui sert à éviter de recalculer et rerendre des parties de templates qui ne changent pas souvent sans repasser par le moteur de rendu et twig utilise le cache applicatif de symfony `index.html.twig`
        > composer require twig/cache-extra
    > Pour créer une variable globale dans twig `config/packages/twig.yaml` - `gloabls`

**User**
- php bin/console make:user : Permet de créer l'entité utilisateur
    > Sauvegarder en base de données [y]
    > La propriété qui sert de nom d'affichage de l'utilisateur [email]
    > hasher le mot de passe [y]

- La configuration pour le composant sécurité dans `security.yaml`
    > Dans les controllers on peut vérifier le rôle grâce à `$this->denyAccessUnlessGranted('ROLE_USER')` ou `$security->isGranted('ROLE_ADMIN')`
    > Sur les controllers ou les class on n'a l'attribut `#[IsGranted('ROLE_ADMIN')]`
        > `IS_AUTHENTICATED` Permet de vérifier si on n'est authentifié
        > `IS_AUTHENTICATED_FULLY` !! si on n'est totalement connecté, correspond au rôle user

- Si on veut se connecter et qu'on est redirigé parcequ'on n'est pas connecté `AppAuthenticator` - `AuthentificationEntryPointInterface` - `start` qui permet d'indiquer comment on vas démarrer notre processus d'authentification, donc lorsque l'utilisateur n'est pas connecté et essai d'acceder à une ressource protégée l'authenticator qui fonction comme un `entrypoint` est utilisé et décidé quoi faire, sachant qu'on ne peut avoir qu'un `entry_point`, pour definir le composant qui est responsable de l'entry_point on vas dans `security.yaml`

- On n'a crée `APIAuthenticator` ou `APIKeyAuthenticator` qui vont nous permettre de se connecter via une clé d'api et ils sont configurés dans `security.yaml`

- Pour bloquer l'accès à un utilisateur sur le site on pourrait tenter de modifier le `user_provider`, par defaut symfony dispose d'une class pour gérer qui les `UserChecker`, on n'a crée `StupidAuthenticator` qui permet de se connecter avec l'url `?auth=1`, on utilise `NoJohnUserChecker` pour le bloquer la connexion à un utilisateur
- !! la gestion des messages d'erreur comme `Access denied` on peut les capturer en créant un `AccessDeniedHandler`

- php bin/console make:auth | php bin/console make:security:form-login : Pour la connexion
    > form-login [1]
    > Le nom de la class qui servira d'authenticator [AppAuthenticator]
    > La route de logout [y]
    > remember-me [0] user coche la case, [1] coché par défaut

    > Dans le `AppAuthenticator->onAuthenticationSuccess` on défini la route de redirection après connexion
    `SecurityController` controller de la partie authentification
    > Si on veut permettre à l'utilisateur de se connecter avec son email ou username, on vas dans `AppAuthenticator->authenticate` on a un second paramètre dans le `UserBadge`

- php bin/console make:registration-form : Pour l'inscription
    > Pour ajouter une règle #[UniqueEntity] sur `User` pour éviter la création de comptes en double [yes]
    > Email de vérification [yes]
    > Pour inclure l'id de l'utilisateur dans la vérification et lorsqu'il va cliqué sur le lien il sera directement connecté sinon on lui demandera de se connecté [no] - Se trouve dans `EmailVerifier` - `$signatureComponents`
    > L'email qui va être utiliser pour envoyer la confirmation
    > Le nom associé à l'email
    > Pour authentifier automatiquement l'utilisateur après son inscription même si son email n'est pas encore vérifié [no]
        > Si on a inclus l'id de l'utilisateur dans la vérification on doit choisir [no] sinon il ne pourra pas s'authentifié à cause de a condition `->andWhere('u.isVerified = true')` de la méthode `findUserByEmailOrUsername` du `UserRepository` 
        > Si on n'a pas !! on peut choisir [yes] mais on ne doit pas mettre la condition `->andWhere('u.isVerified = true')` de la méthode `findUserByEmailOrUsername` du `UsuerRepository`
            > On peut le laisser accéder à la page de vérification d'email et pour les autres pages il faut qu'il est le rôle `ROLE_VERIFIED` de `User`

- Pour la vérification par email il faut le bundle `composer require symfonycasts/verify-email-bundle`, on peut aussi utiliser un token avec expiration
- !! utiliser le système de force brute de l'authenticator de symfony on vas dans `config/packages/security.yaml` - `login_throttling`
    > composer require symfony/rate-limiter

**Voter** - Un système de permission avancée
- php bin/console make:voter RecipeVoter : Permet de créer un voter
    > Si on lance la vérification, ce sont tous les voter qui vont votés donc s'il y'a en a 1 qui vote oui tous le temps il va toujours passer, on peut aussi changer la strategie, mais par defaut c'est `affirmative` si un des voter vote oui alors il passe, pour changer le comportement on vas dans le fichier `security.yaml` - `security`
        ```yaml
        access_decision_manager:
            strategy: unanimous -- S'il y'a un non dedans il refuse
            allow_if_all_abstain: false -- Si aucun dit qu'il support la requête, par defaut on n'a pas la permission
        ```

**Rate Limiter** - Permet de limiter un utilisateur
- composer require symfony/rate-limiter : ..a configuration dans `config/packages/rate_limiter.yaml`
    > Puis on injecte `RateLimiterFactory` dans le controller et le nom doit faire reférence à la clé après `rate_limiter` du fichier de configuration

**Doctrine**
- php bin/console doctrine:database:create : Pour créer la base de données
- php bin/console make:migration : !! une migration en lisant nos entités
- php bin/console doctrine:migrations:migrate ou `d:m:m` : !! exécuter la migration et met à jour la base de données
- php bin/console doctrine:migrations:execute --down MIGRATION_ID : !! supprimer une migration
- php bin/console doctrine:database:drop --force : !! supprimer la base de données
- php bin/console doctrine:schema:validate : !! vérifier l'état du schéma
- php bin/console doctrine:schema:update --force : !! de forcer la mise à jour le schema de la base de données après une modification d'entité
    > php bin/console doctrine:schema:update --dump-sql : !! voir plus de détail sur les propriétés qui n'ont pas été perisisté dans la base de données
- php bin/console doctrine:migrations:status : !! les informations sur la migration 
    > Pour le problème dans les entités, on prend le timestamps du preview et on mais la base de données à cette étapes là
    > php bin/console doctrine:migrations:migrate DoctrineMigrations\Version20250102143548 : On supprime ensuite la dernière migration et en généré une nouvelle avec les changement qu'il y'a eu dans mes entités
- php bin/console dbal:run-sql 'SELECT * FROM product' : !! exécuter un requête dans le terminal

- Les relations
    > `ManyToOne` chaque recette à une categorie et une categorie à plusieurs recettes
    > `OneToMany` !! sens inversé si on sur l'entité `Category`
    > `ManyToMany` va crée une table intermediaire pair pair
    > `OneToOne` une recette peut avoir une seule categorie et &&
- Dans les entités quand on n'a dans la relation `inversedBy` signifie que la relation vient de toi et `mappedBy` tu mappe la relation
- !! le `ManyToOne` lorsqu'on veut supprimer on doit d'abord definir la valeur `cascade: ['remove']` ou `['null']`
- Si on met `cascade: ['persist']` sur un attribut de type relation dans une entité, cela signifie que l'entité liée sera automatiquement persistée, enregistrée en base de données en même temps que l'entité principale, si elle ne l'est pas déjà
- L'option `fetch: 'EAGER'` permet de faire la récupération de données en faisant une jointure, mais l'inconveinet est que dès qu'on voudra récupérer le moindre article il associé la catégorie, peut régler le problème `n+1`

- Si on essaie de supprimer un utilisateur `user` qui est référencé dans une autre table `recipe` via une clé étrangère `user_id` et `mysql` empêche la suppression parce que cela casserait la relation vu qu'un enregistrement dans `recipe` pointe encore vers cet utilisateur
    > Sol 1 : On supprime ou désassocie les recettes avant de supprimer l'utilisateur
    > Sol 2 : !! change le comportement de la contrainte en base de données `ON DELETE CASCADE` cela supprime automatiquement les recettes liées quand on supprime l'utilisateur dans `Recipe` ou `ON DELETE SET NULL`
        > On peut le faire dans l'attribut de colonne `#[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]`
        > Ou dans l'attribut de relation `#[ORM\ManyToOne(inversedBy: 'recipes', cascade: ['persist', 'remove'])]`

- Si dans `config/package/doctrine.yaml` - `auto_mapping` est à `false` la récuperation automatique des objet gràce au params `{id}` ou `{slug}` dans les controllers sera désactiver
- Si `flush()` échoue, une `Doctrine\ORM\ORMExceptionexception` est levée, voir transactions et concurrence dans doc

- Les évènements doctrine `PropertyListener`
    > `preRemove` avant la suppression de l'entité
    > `postRemove` après la suppression de l'entité
- On a aussi les filtres doctrine..

**Formulaires**
- php bin/console make:form PostFormType : Permet de créer un formulaire
- Par défaut les formulaires n'ont aucune class associés, si on veut leur mettre un thème
    > Dans `config/packages/twig.yaml` - `twig` on rajoute `form_themes: ['bootstrap_5_layout.html.twig']`, doc
- Pour prendre en compte les autres mèthodes dans les formulaires comme `DELETE` ou `PUT`
    > Dans `config/packages/framework.yaml` - `framework` on rajoute `http_method_override: true`
- !! valider les données, on peut le faire au niveau des formulaires ou entités
    > Dans le `formBuidler` avec l'attribut `"constrains" => ..`
        > Pour ne pas afficher toutes les erreurs en même temps à l'utilisateur, dans ce cas si la première contrainte pose une erreur il affiche lui seule
            ```php
            new Sequentialy([
                new Lenght(min: 10),
                new Regex('..')])
            ```
    > Sur l'`entité` avec l'attribut `#[Assert\..]` au dessus d'une propriété `private string ..` dans une entité
- L'option `mapped` à false dans un `formBuilder` permet de dire que le champ ne correspond à rien dans notre entité, c'est un champ qui va être disponible dans les données du formulaire mais ne pas essayer de faire un `set..` dessus et est accessibles dans un contrôleur
    > On peut récupérer la donnée du formulaire `$form->get('agreeTerms')->getData()` ou `->set..` pour modifier
- L'option `by_reference` à true permet à symfony d'utiliser `$post->getTags()->add($tag)` sinon `$post->setTags($collection)`
- Pour ajouter une erreur manuellement dans un formulaire
    > On peut faire `$form->get('email')->addError(new FormError('L'email est déjà..'))` ou avec `new ConstraintViolation..`

- Pour créer une contrainte personnalisé
    > php bin/console make:validator BanWordValidator : Va crée 2 class, une `BanWord` qui va représenté la contrainte et l'autre `BanWordValidator` permet de faire notre logique

- Si on utilise une entité comme données du formulaire, lors de la crétion du `formType` on met le namespace complet de l'entité `\App\Entity\DTO\ContactDTO`
- Pour éviter que le `setter` recois un null si l'utilisateur enlève le `required` via l'inspecteur on fais `'empty_data' => ''` dans le `formBuilder` va mettre cette valeur par défaut si le champ est null et dans l'entité `private string .. = ''` pour être logique

- Pour plus d'option sur les formulaires `RecipeFormType`
- Pour personnaliser le formulaire on a plusieurs niveaux
    > Sol 1 : Le rendu manuel du formulaire avec `form_label` pour la label et `form_widget` pour le champ san le `form_row`
    > Sol 2 : Le `Form Themes` pour changer le html globalement
        > On crée un fichier twig comme `form.html.twig` puis dans `config/packages/twig.yaml` - `form_themes` on le rajoute

- **FormType personnalisé** - On a `SearchableEntityType` pour gérer les types de champ `ManyToMany` dans `PropertyFormType`
    > Pour la relation `ManyToMany` le `formBuilder` génère un `EntityType` qui génère un select multiple mais le problème est que si on a des miliers de données on aura un select qui avec des miliers d'options, donc l'idée est d'avoir un select avec des options déjà selectionnées et lorsqu'on tape une valeur ça fais une recherche pour pouvoir rajouter des options à partir de celle ci, on peut utiliser le `EntityType` qui est bien mais a l'inconvenient d'attendre un `queryBuilder` pour construire notre requête or nous on veut remplir avec des informations qui vont être bien prédéfini qui sont seulement les données qui ont déjà été renseignées et on n'a aussi `ChocieType` mais qui a le même problème
    > !! la librairie frontend on a `tom-select` dans `app.js` pour styliser le select et faire la recherche, dans `AdminOptionController` on a la route de recherche

- **Validator**
    > La contrainte `Lenght` permet de valider une chaîne de caractère - (min: 10, max:20, minMessage: "Minimum {{ limit }}", maxMessage: "")
    > !! `Email` pour valider un email - (message: "Votre email {{ email }} n'est pas valide "{{ value }}"")
    > !! `NotBlank` !! indiquer qu'un champ est requis - (message: "Le nom est obligatoire")
    > !! `Regex` !! valider une expression réguliaire - (pattern: "/^\d{10}$/", message: "Doit contenir 10 chiffres")
    > !! `isFalse` on s'attend à une valeur false - (message: 'Ne doit pas être coché')
    > !! `isTrue` on s'attend à une valeur true - (message: 'Vous devez accepter')
    > !! `Positive` si on autorise la valeur null il ne vas pas fonctionné, dans ce cas on met `NotBlank` avant
    > !! `Negative` pour avoir une valeur négative
    > !! `Date` !! une date
    > !! `DateTime` !! une datetime
    > !! `Time` !! un temps
    > !! `Choice` pour faire des choix, on n'a aussi les options `multiple`,  `min`, `max`.. - (choices: ['Homme', 'Femmme'], message: "Choix invalide")
    > !! `Range` !! un choix, on n'a aussi les options `notInRangeMessage`, `minMessage` et `maxMessage` - (min: 1, max: 30)
    > !! `NotNull` !! avoir une valeur non null - (message: 'Valeur requis')
    > !! `Url` !! une url - (message: "URL invalide")
    > !! `File` !! un fichier - (maxSize: "2M", mimeTypes: ["application/pdf"], mimeTypesMessage: "Le fichier .. un PDF")

**Evènement listener** - !! équivaut au `middleweare`
- php bin/console make:listener DogListener : Le `listener` est une class qui va venir se grèfé et s'activé lorsqu'un évènement à lieu
    > Va suggéré des évènement par défaut comme [kernel.response] pour la transformation de données
    > [kernel.request] pour intercepter une requête..
- php bin/console make:subscriber : Le `subscriber` peut avoir plusieurs méthodes et s'abonner à plusieurs évènements
    > Va suggéré des évènement par défaut comme [kernel.controller]

**Messenger** - Un système qui permet de gérer des tâches `async` ou fil d'attente au niveau de notre application
- Par défaut les mails s'envoient sur `messenger`, si on décommente la ligne `# sync: 'sync://'` qui permet de créer une nouvelle fil qui est sync pour ne pas qu'elle passe par `messenger` et pour les éléments de la partie `routing` on les met tous `sync`, ensuite si on veut configurer symfony pour qu'il envoi les mails à un endroit comme `mailpit` qui est un faux serveur mail, on vas dans le fichier `.env` et on met le dsn du serveur mail `MAILER_DSN=smtp://localhost:1025`

- Pour envoyer les mails vers la fil d'attente `async`, configuration `config/packages/messenger.yaml`
    > `transports` permettent d'envoyer des messages vers notre système de fil d'attente
        > async
            > dsn : Permet d'indiquer comment vont être traités les messages avec l'env `MESSENGER_TRANSPORT_DSN` qui par défaut utilise doctrine pour stocker les messages dans la base de données avec la table `messenger_messages`
            > options : Les options de configuration liées à chaque transport
                > `retry_strategy` - `max_retries: 3` : Si une tâche échoue elle va être retenté 3 fois et si ça ne marche pas elle sera renvoyé à la fil d'attente `failed`
    > `routing` permet de spécifié sur quel transport notre message vas être envoyer

- Pour traiter les tâche une fois envoyé dans la fil d'attente
    > php bin/console messenger:consume async : Va démarer messenger et les emails seront envoyés, `-vv` pour avoir plus d'informations ou autre `params --limit=10 --time-limit=60 -vv`
        > Lorsqu'on n'est en production il faudra faire en sorte de traiter les fil d'attente en permanence avec `SuperViseur` plus d'infos dans la doc

- php bin/console messenger:remove-messages async --processed : Pour supprimer automatiquement les messages traités dans doctrine car il ne le sont pas par défaut

- Pour faire notre propre traitement, on n'a envoyé le message dans le `AdminRecipeController` - `edit`
    > php bin/console make:message NomMessage
        > Dans quel transport on veut le mettre
    > Va crée 2 fichiers et modifié `messenger.yaml`
        > `RecipePDFMessage` va permettre de passer de l'information vers notre fil d'attente et cette objet vas être `serializé` dans notre base de données donc on vas juste y mettre des informations scalaire si possible comme l'id de la recette
        > `RecipePDFMessageHandler` va reçevoir un message et faire le traitement qui est nécéssaire

- En cas de changement de code il faut redémarrer messenger car quand il est lancé ça utilise le code du moment, on peut aussi utiliser la commande
    > php bin/console messenger:stop-workers : Permet de relancer les services workers
- !! d'échec ou d'erreur après les tentatives il envoi dans la fil d'attente `failed`
    > php bin/console messenger:failed:show : Pour voir les commandes qui ont échoué
    > php bin/console messenger:failed:retry : Pour choisir ce qu'on veut faire, retry ou delete
- Pour recevoir une notification lorsqu'un message a échoué on n'a l'évènement `WorkerMessageFailedEvent` qui est renvoyer lorsqu'une tâche renvoi une erreur - `FailedMessageSubscriber`
- !! relancer ou supprimer manuellement les tâches dans un dashbord, tous les transports qu'on utilise doivent implémenter `ListableReceiverInterface` qui permet de représenter un recever qui va être capable de lister les informations - `FailedJobRepository` - `AdminController`

- Par.. `messenger` utilise une base de données `mysql` ce qui n'est pas optimal donc on.. `redis` en configurant une base de données sur l'hébergeur dans `Redis` puis dans `.env.local` - `MESSENGER_TRANSPORT_DSN`
- php bin/console debug:messenger : Plus d'information sur l'état de notre système comme les différents messages ou handler

- Si on veut envoyer des emails via un fournisseur de messagerie pris en charge on peut installer le pont `mailgun`,on a aussi des services `smtp` fiable comme `gmail`, `sendinblue`, `mailjet`
    > composer require mailgun-mailer : !! doc, puis dans le `.env` - `MAILER_DNS` on.. smtp://username:password@smtp...com:587?encryption=tls&auth_mode=login

- Pour activer les logs ou exception pour savoir si un mail a échoué - `mailer.yaml` - `mailer` - `logging: true`
- Pour bien formater les emails on n'a des packages comme `Inky` ou `Mjml`..

**Cache**
- composer require symfony/cache
- La configuration `config/packages/cache.yaml`
    > `cache.app` le cache applicatif
        > Le.. utilise `CacheInterface` qui permet d'accéléré la logique métier, mettre en cache les résultats de requêtes en base de données ou d'appels api et supporte plusieurs backend comme `Filesystem`, `Redis`, `Memcached`, `APCu`..
    > `cache.system` le cache système
        > Le.. utilise `FilesystemAdapter` qui permet d'optimisé le fonctionnement interne de symfony et réduire le temps de boot et de compilation dans `var/cache` et il est le le plus rapide en production
- Si on met en cache un objet `Post` doctrine sérialize l'entité mais une relation `OneToMany` est un proxy doctrine or quand on recharge l'objet depuis le cache, doctrine n'est plus là pour initialiser ce proxy ex: `post.pictures` devient null
- Le cache `HTTP` permet d'améliorer les performances en réduisant le nombre de requêtes vers le serveur et en réutilisant les réponses déjà calculées `HomeController` - `index`
    > Pour activer ce système `config/packages/framework.yaml` - `framework` - `http_cache` permet à symfony d'envoyer les bons headers `Cache-Control` et `ETag`

**Traduction**
- composer require symfony/translation : ..a configuration dans `config/packages/translation.yaml`
- Pour traduire on regarde dans de barre de `debug` le `Domain` de traduction, si c'est `messages` on crée un fichier `messages.fr.yaml` dans `translations/` et à l'intérieur on mais le `Message ID: traduction`
- Pour générer de manière dynamique les traductions, on n'a aussi le formatage avancé `icu`
    > php bin/console translation:extract --dump-messages fr : Pour le debug
    > php bin/console translation:extract --force fr --format=yaml : !! générer dans le fichier de traduction
        > Par défaut ça ne réagit pas au niveau des formulaire car ce sont des chaînes de caractère, pour y remedié il faut entouré le label par la fonction `t()` qui crée un objet de type `TranslatableMessgae` ensuite le système d'extraction vas être capable de capturer, doc

- Pour faire une traduction dynamique selon le local de l'utilisateur
    > Dans `User` on a ajouté une propriété `locale` default `fr`
        > Pour changer la locale il y'a un service
            > php bin/console make:listener UserLocaleListener - kernel.request

- Par l'url, dans `HomeController` params `{_local}`
    > `Glablement` => `routes.yaml` => va prefixé toutes les langue

- Pour traduire les données provenant de la base de données
    > La 1ère aproche c'est d'avoir 2 base de données avec des configurations `.env` qui vont variées d'un site à l'autre, un ira sur la base de données française et l'autre sur l'anglaise
    > La 2ème est d'utiliser la même base de données pour les traductions, pour ça il y'a une extension disponible `StofDoctrineExtensionsBundle`

**Tests** - Plus d'infos `README.md` de `JavaScript`
- php bin/console doctrine:database:create --env=test : On configure le fichier dans `.env.test` - `DATABASE_URL`
- php bin/console doctrine:schema:update --env=test --force : Pour créer la structure de mes tables
- php bin/console --env=test doctrine:fixtures:load : !! charger les fixtures dans la base de données de test
- php bin/console make:test : !! créer un fichier de test
    > Type de test
        > `TestCase` pour les tests classique
        > `KernelTestCase` !! ayant accès aux services de symfony - [Entity]
        > `WebTestCase` !! similaires à ceux d'un navigateur - [Controller]
        > `ApiTestCase` !! d'api
        > `PantherTestCase` !! d'intégrations en utilisant un vrai navigateur
    > Le nom du fichier
- ./bin/phpunit --filter AppTest : Pour exécuter un seule test
- La configuration `phpunit.xml`
- Le `code coverage` indique le pourcentage de code tester
    > ./vendor/bin/phpunit --coverage-text : Pour avoir le rapport en text
    > ./vendor/bin/phpunit --coverage-html tmp/ : !! en html
        > `vert` ligne exécutée par au moins un test
        > `rouge` ligne jamais exécutée, aucun test ne l’a couverte
        > `jaune` ligne partiellement couverte, un `if` testé que dans un seul cas

- **Panther** - Un package pour faire du test avec dans le navigateur
    > composer require --dev symfony/panther

- 
- **Request**
    > ->get(string $key, $default = null) : Pour récupérer la valeur d’un paramètre GET ou POST
    > ->has(string $key) : !! vérifier la présence d’un paramètre GET ou POST
    > ->all() : !! récupérer tous les paramètres POST
    > ->query->get(string $key, $default = null) : !! un paramètre GET
    > ->query->all() : !! tous les paramètres GET
    > ->attributes->get(string $key, $default = null) : !! un attribut de la requête, les variables de route
    > ->attributes->all() : !! tous les attributs
    > ->files->get(string $key, $default = null) : !! un fichier uploadé
    > ->files->all() : !! tous les fichiers uploadés
    > ->server->get(string $key, $default = null) : !! avoir accès aux variables serveur
    > ->server->all() : !! à toutes les variables serveur

    > URL
        > ->getPathInfo() : Permet de retourner la partie uri sans domaine `/articles/42`
        > ->getRequestUri() : !! l'uri complète avec query string `?page=2`
        > ->getScheme() : !! le protocole, http ou https
        > ->getHost() : !! le nom de domaine
        > ->getPort() : !! le port utilisé
        > ->getBaseUrl() : !! la partie `base` du chemin
        > ->getUri() : !! l'url complète

    > Les méthodes HTTP
        > ->getMethod() : Permet de retourner la méthode HTTP, GET, POST, PUT, DELETE
        > ->isMethod(string $method) : !! vérifier si la méthode HTTP correspond
        > ->isXmlHttpRequest() : !! si la requête est faite en ajax
        > ->getContent() : !! récupérer le contenu brut de la requête, utile pour api json, xml..
        > ->headers->get(string $key, $default = null) : Pour avoir accès aux headers HTTP
        > ->headers->all() : !! à tous les headers
        > ->headers->has(string $key) : !! vérifier la présence d’un header
        > ->isSecure() : !! si la requête utilise HTTPS
        > ->getClientIp() : !! récupérer l’adresse ip du client en tenant compte des proxys

    > Les cookies et sessions
        > ->getSession() : Permet de retourner l’objet session associé
        > ->hasSession() : !! vérifier si la requête possède une session
        > ->setSession(SessionInterface $session) : !! d'associer une session à la requête
        > ->cookies->get(string $key, $default = null) : !! récupérer un cookie
        > ->cookies->all() : !! tous les cookies

    > Langue
        > getPreferredLanguage(array $locales = null) : Permet de récupérer la langue préférée du client selon l'en-tête `Accept-Language`
        > getLanguages() : !! les langues acceptées
        > getCharsets() : !! les jeux de caractères acceptés

- **Response**
    > ->getContent(): string : Permet de retourner le contenu actuel de la réponse
    > ->setContent(string $content): static : !! définir ou modifier le contenu de la réponse
    > ->getCharset(): string : !! retourner le charset utilisé ex: `UTF-8`
    > ->setCharset(string $charset): static : !! définir le charset
    > ->isEmpty(): bool : !! vérifier si la réponse ne doit pas contenir de contenu ex: code `204`
    > ->hasHeader(string $key): bool : !! la présence d’un header
    > ->getProtocolVersion(): string : !! retourner la version HTTP 
    > ->setProtocolVersion(string $version): static : !! définir la version HTTP

    > Les entêtes
        > ->headers->set(string $key, string $value, bool $replace = true) : Pour ajouter ou modifier un header
        > ->headers->get(string $key) : !! récupérer la valeur d’un header
        > ->headers->remove(string $key) : !! supprimer un header

    > Le statut HTTP
        > ->getStatusCode(): int : Permet de retourner le code HTTP actuel
        > ->setStatusCode(int $code, string $text = null): static : !! définir le code HTTP et optionnellement le texte
        > ->isSuccessful(): bool : !! vérifier si le code HTTP est entre 200 et 299
        > ->isRedirection(): bool : !! si le code HTTP est entre 300 et 399
        > ->isClientError(): bool : !! si le code HTTP est entre 400 et 499
        > ->isServerError(): bool : !! si le code HTTP est entre 500 et 599
        > ->isOk(): bool : !! si le code HTTP est 200
        > ->isForbidden(): bool : !! si le code HTTP est 403
        > ->isNotFound(): bool : !! si le code HTTP est 404

    > L'envoi de la réponse
        > ->send() : Permet d'envoyer les en-têtes HTTP et le contenu au client
        > ->sendHeaders() : !! uniquement les en-têtes
        > ->sendContent(): !! uniquement le contenu

    > La gestion des cookies
        > ->headers->setCookie(Cookie $cookie) : Pour ajouter un cookie à la réponse
        > ->headers->clearCookie(string $name, string $path = '/', string $domain = null) : !! supprimer un cookie

    > Par défaut symfony propose des classes dérivées et des méthodes statiques pour créer des réponses spécifiques
        > `RedirectResponse` pour redirections
        > `JsonResponse` !! retourner du json
        > `StreamedResponse` !! flux
        > `BinaryFileResponse` !! fichiers à télécharger
- 

**Les pages d'erreurs**
- Pour les personnaliser on crée dans `templates`.. bundles/TwigBundle/Exception/error404.html.twig..403.. `error.html.twig` sera utilisé pour toutes les autres erreurs html y compris le `500`
- On peut taper dans l'url `/_error/404` pour voir une page d'erreur dans le mode `dev`

**Symfony Notifier** - Un composant symfony qui permet d'envoyer des `SMS`
- Pour envoyer des sms on a différents type de transports
    > Le transport `twilio`
        > composer require symfony/notifier symfony/twilio-notifier
            > On récupère nos identifiants twilio sur leur site `Account SID` - `Auth Token` - `Numéro Twilio`
            > !! ajoute les identifiants twilio dans `.env`
            > !! configure le transport notifier dans `config/packages/notifier.yaml`
            > !! envoi un `SMS` via un controller

**Workflow** - Une machine à état pour organiser le code d'une appplication
- composer require symfony/workflow : ..a configuration dans `config/packages/workflow.yaml`
    > `state machines` permet d'avoir un système qui dans un unique état
    > `workflow` !! à une machine de se situé dans plusieurs état à la fois et pour pouvoir passer une transition (passer d'un état à un autre) il faut qu'elle est eu tous les étapes précédents
    > `marking_store` !! de spécifier ou on vas sauvegarder l'état de notre machine
- php bin/console workflow:dump blog_publishing --dump-format=mermaid : Permet de publier un schéma workflow, génère un graph qui va pouvoir être utilisé par des librairies
    > `--dump-format=mermaid` pour utiliser un autre format, mermaid qui est supporté par pas mal d'outil
    > Un outil pour voir notre état `stackedit.io`
        > ```mermaid
            graph LR
            place0(["draft"]) -- ensuite on colle le schema
- Par défaut, lorsque quelque chose change dans la machine à état des évènements vont être émis `doc` et on va pouvoir les capturer - `BlogPublishingSubscriber`

**Logger** - Permet d’écrire des messages dans des fichiers de log `HomeController` - `index`
- La configuration dans `config/packages/monolog.yaml`
- Les log sont stockés dans `var/log/dev.log` ou `var/log/prod.log`
- Le niveau de log
    > `->debug()` pour les détails utiles uniquement pendant le developpement - `DEBUG`
    > `->info()` !! les informations sur le fonctionnement normal - `INFO`
    > `->notice()` !! évènements intérressant mais pas problématique - `NOTICE`
    > `->warning()` !! quelque chose de potentiellement anormal - `WARNING`
    > `->error()` !! erreurs qui n’a pas stoppé l’application - `ERROR`
    > `->critical()` !! fonctionnalités cassée - `CRITICAL`
    > `->alert()` l'application ne peut pas continuer sans intervention - `ALERT`
    > `->emergency()` !! panne totale - `EMERGENCY`
- Pour voir les logs dans la console
    > tail -f var/log/dev.log
- On peut.. un dashboard qui lit les logs dans notre application

**Cron - Command**
- php bin/console make:command app:backup : Pour créer une commande
- php bin/console app:backup : !! tester la command
- 0 0 * * * /usr/bin/php /chemin/vers/bin/console app:backup : Un cron pour exécuter la script tous les jours à minuit, `>> /var/log/error.log 2>&1 logs` pour sauvegarder le résultat, les erreurs et rediriger les logs dans un fichier `o`

**Production**
- Hébergement mutualisé `cPanel`
    > Step 1 : On vérifie si l'hébergeur supporte notre version de php et on peut changer la version ou activer des extensions dans `Php`
    > Step 2 : !! configure le serveur web pour pointer vers le dossier `public/` de notre application
        > Pour un domaine on vas sur la partie `Domaine` et si on utilise un `sous-domaine` on vas sur la partie `Sous-domaines` ensuite on entre les informations
            > Si l'hébergeur utilise le serveur `apache`, on doit installer le package `symfony/apache-pack` pour avoir le fichier `.htaccess` dans lequel on peut rajouter la règle qui permet de forcer la redirection vers le `https`
                > composer require symfony/apache-pack : Permet de générer ce fichier dans le dossier `public/`
            > Dans le cas `nginx`, doc
    > Step 3 : !! la base de données `mysql` ou `postgresql`
        > !! crée la base de données ainsi qu'un utilisateur associé avec tous les privilèges, sur certains hébergeurs on a un assistant de base de données ou `Manage My Databases`
    > Step 4 : !! deploye l'application en ligne via `ssh`, `git` ou `ftp`
        > Pour le `ftp` on peut crée des comptes dans le panneau `FTP Accounts`
        > !! se connecter au serveur via `ssh` on vas dans `Autorisation ssh` on entre notre adresse ip pour ajouter l'autorisation et ensuite on fais le processus qui permet d'envoyer notre clé ssh pour se connecter au serveur
        > !! `git` on versionne le projet sur dépôt distant, on peut le faire sur `Github` ou créer un depôt git depuis l'hébergeur `Git Version Control`..
            > On décoche `Clone a Repository` vu qu'on vas créer un nouveau dépôt `Repository Path` puis on.. un dossier `git/..` qui contiendra les projets, le `Repository Name` on.. le nom du domaine va servir pour le listing au niveau du `cPanel` et on aura les intructions pour ajouter le remote
            > Si.. dépôt distant on doit créer une clé ssh sur notre serveur et le renseigné sur `Gitbub`

    > En production la priorité est donnée au fichier `.env.local.php` sinon `.env.local` dans lequel on met les identifiants du serveur ansi que nos clés api vu que le fichier `.env` est versionné
        > Sol 1 : On peut crée le `cp .env .env.local`
            > `APP_ENV=prod` pour changer l'environnement
            > `APP_SECRET` utile pour le remember me
            > `DATABASE_URL` mysql://`nom de la bd`:`mot de passe`.../`nom utilisateur`..serverVersion=`version de la bd`-MariaDB - si on l'utilise, permet à doctrine de générer des requêtes sql adapté à cet environnement
            > `MAILER_DSN` smtp://user:pass@smtp.example.com:port
                > On peut utiliser `native://default` qui utilise la fonction native de php ou configurer un serveur `smtp` dans ce cas on vas dans `Email Accounts` ou `Comptes de messagerie` on verra notre utilisateur ainsi qu'un compte de messagerie du genre `bakayoko@socafpesage.com` ensuite on `Check email` on a normalement reçu un email qui nous explique la configuration `smtp`
                > php .. mailer:test support@g..
        > Sol 2 : !! le `.env.local.php` pour éviter qu'il lise le `.env` systématiquement et qu'il le parse, plus optimisé
            > composer dump-env prod
    > Command
        > composer install --no-dev --optimize-autoloader : Pour installer les dépendances en mode production
        > php bin/console cache:clear --env=prod : Pour vider le cache pour la production
        > php bin/console cache:warmup --env=prod : !! générer le cache
        > php bin/console secrets:set APP_SECRET : !! regénérer la clé
        > php bin/console doctrine:database:create --if-not-exists : !! créer la base de données
        > php bin/console doctrine:migrations:migrate --no-interaction : !! migré les tables
    > On peut donner les permissions au dossier dans lequel notre application va écrire comme `var/` si nécéssaire
        > chmod -R 775 var/
    > Pour activer le `https` on configure le `SSL` dans la partie `Gestion des sites` ou `Let..`
    > En cas d'erreur serveur on vas dans la partie `Erreurs et accès` pour voir les logs ou dans symfony `tail -n 300 -f var/log/prod.log`
    > Pour automatiser tout ce processus on peut utiliser `Make` via `Makefile` ou les `Hooks` via `git`
    > !! utiliser un package `dev` en `prod` on l'install d'abord sans `--dev` puis dans `bundles.php` on le.. `['all' => true]`
    > !! `messenger` si on n'a pas accès à `supervisor` dans l'hébergeur on peut utiliser les tâches `cron` ex.. dans la partie commande, le time limit doit correspondre au temps du cron ex.. `5min`
        > /usr/local/bin/php /home/../bin/console messenger:consume --time-limit=295 --memory-limit=128M async..

**Update** - La mis à jour de symfony
- La.. à une `version mineur`
    > On vas dans `composer.json` puis `CTRL + h` on met `8.0` la version à remplacé ensuite `8.5` pour les dépendances symfony et `composer update "Symfony/*"`, `--with-all-dependencies` pour mettre à jour les dépendances auxquelles il dépend aussi..

**Api Platform** `api-platform.com`
- composer require api : On aura accès à l'url `/api` qui est une documentation généré grâce à `swagger ui` capable de lire les fichiers de définition `openApi` et de générer une interface graphique
    > Ou.. via ses release `github` qu'on peut utiliser si on veut directement mettre en ligne notre `ApiPlatfrom`
- La configuration dans `config/packages/api_platform.yaml`
    > Pour activer les support des opérations personnalisées avec l'option `controller`, dans la configuration on.. `use_symfony_listeners: true`
- php bin/console make:entity --api-resource : Pour créer une ressource d'api ou on peut utiliser les entités de symfony
- Le principe de `serialization` est de faire en sorte de rajouter ou modifier les champs qui sont renvoyés au niveau de l'api, le processus de `serialization` est que lorsqu'on va récupérer des informations automatiquement on vas récupérer des entités qui proviennent de la base de données qui vont être converti en `json` par la `serialization` qui fonctionne en 2 temps, on a la `normalisation` qui va transformer notre entité sous forme de tableau php qui va être encodé en `json` mais pour normalizé par défaut il prend tous les champs de type simple ce qui fais qu'il ne prend pas la partie `category`, on peut piloter quel champ vont être convertir au travers de groupe de `normalization` et on la `denormalisation` qui fais les choses en sens inverse, de changer ce qui va être reçu depuis la requête et intégré dans l'objet final
- 
    > Lors de la récupération des articles on aura un retour serveur avec `@context`, `@id`, `@type`.. permet de donner des informations sur notre ressource qui a été crée ou récupéré grâce au média type `application/ld+json`
        > Le `@id` est une uri ou url qui permet de récupérer l'article
        > !! `hydra:member` va contenir l'ensemble de nos articles
        > !! `hydra:totalItems` pour le nombre total d'article par page
        > !! `hydra:view` on `hydra:first`.. !! la pagination et si on n'est à la dernière page le `hydra:next` n'est pas retourné
    > Pour repondre à différents format de contenu `config/packages/api_platform.yaml` - `formats`
    > Les requêtes de type `PATCH` requière le format `application/merge-patch+json` qui permet de définir des opérations à éffectué `config/packages/api_platform.yaml` - `patch_formats`
    > Pour liée une `Category` à un `Post` dans la requête du `Post` on lui envoi l'uri `@id` de la catégorie `{"category" => "/api/category/1"}`, va repondre avec un `200` mais ne va rien faire au niveau de la base de données donc il faut flitrer les champs à affichés et à masqués au niveau des retour grâce au groupe de `normalization` et `denormalization` pour les champs à modifié, ici avec l'option `denormalizationContext`
        > !! que quand on crée un commentaire il renseigne l'utilisateur il va falloir ajouter un évènement qui dit lors de la création d'un commentaire il faudra insérer l'utilisateur, on peut se branché sur un évènement de `ApiPlatform` ou crée un `controller`
    > !! récupérer des informations de la `Category` du `Post` on le fais au travers l'attribut `#[Groups]`
    > !! que `ApiPlatform` renvoi un champ s'il est `null` dans `normalizationContext` on.. `skip_null_values' => false` ou dans la configuration
    > !! les validations on.. des groupes de validation avec l'option `validationContext` et peut s'appliquer au sous entités ex: on vas crée une `Category` en même temps que l'on crée un `Post` qui est possible grâce au groupe de `denormalisation` mais pour que la catégorie soit valider on doit utiliser l'attribut `#[Valid()]` sur la propriété `category` de `Post`
    > !! filtrer les données on a l'attribut `#[ApiFilter]`
    > !! modifer les spécification `openapi` on.. l'option `openapi`
    > !! désactiver au lieu de supprimé une route ou rajouter des opérations manuellement on n'a crée `OpenApiFactory`, aussi pour enlever les paramètres sur la documentation pour une opération
    > On peut créer des `DataProvider` personnalisés si on veut récupérer des ressources d'une autre api, dans `Dependency`, Le `DataPersister` ou `State processor` permet de persister les informations
    > Pour limiter les ressources renvoyés par `ApiPlatform` pour n'afficher que les choses qui appartiennent à un utilisateur lors de la récupération `getCollection`, `get`, `put`, `patch` et `delete`
        > Sol 1 : On peut utiliser un filtre doctrine qui s'activera par défaut et rajouté une condition au niveau de la requête mais elle n'est optimal car on veut mettre en place la sécurité pour les autres opérations
        > Sol 2 : !! vas étendre le système de doctrine qui vas faire générer les requêtes grâce au `Query Extension` de `ApiPlatform` qui permet de modifier automatiquement les requêtes doctrine avant qu'elles ne soient exécutées mais n'agit pas sur le `post`, dans `Doctrine/CurrentUserExtension`
    > !! injecter l'utilisateur lors de la création d'une ressource via un denormalizer `Serializer/UserOwnedDenormalizer`, il est recommandé d'utiliser `DataPersister` ou un `EventListener` sur `kernel.view` ou l'option `processor`
    > !! permettre à un utilisateur connecté de voir plus de champs qu'un utilisateur non connecté, on peut changer les groupes utilisés pour la partie serialization dynamiquement `Serializer/PostContextBuilder`
        > Sol 1 : Le `ContextBuilder` permet de gérer les context qui sont générique ce qui est bien de manière globale, ne vas pas fonctionner si on veut gérer le context élément par élément, du genre pour les articles que je possède je pourrais voir les champs `slug` et `online` mais pour ceux qui ne sont pas à moi je pourrais pas voir pour ça on vas utiliser un normalizer `Serializer/PostAPINormalizer`
            > php bin/console make:serializer:normalizer : Permet de créer un normalizer
    > !! gérer l'envoi des fichiers
        > Sol 1 : On peut utiliser le `base64` dans lequel on encode le fichier côté client et le décoder côté serveur mais le problème est que ça va augmenter le poids et va rendre la requête lourde
        > Sol 2 : !! le `Content-Type: multipart/related` qui permet à l'intérieur de notre requête d'avoir plusieurs `Content-Type` associés ce qui permet d'avoir une partie avec notre `binary` et une autre avec notre requête en `json` mais le problème est qu'il est très peu supporter à la fois côté client et serveur
        > Sol 3 : !! a utilisé le `POST: Content-Type: multipart/form-data` le format utilisé lorsqu'on soumet un formulaire `HTML` qui permet d'avoir à la fois des données et des fichiers, pour gérer l'upload on a utilisé `Vich`, vu que l'application qui vas consommer notre api aura besoin du chemin vers l'image on l'a fait dans `PostImageController` pour injecter le chemin dans la propriété `fileUrl`
            > Vu que `ApiPlatform` ne peut pas déserializer une en tête `multipart/form-data` à cause de son évènement `DesarializeListener` mais qu'on veut gérer l'upload de manière générique on a remplacé le déserializer qui est utilisé dans `DesarializeListener.php`, pour éviter l'erreur de dénormalization de l'image dans notre objet on n'a `FileDeserializer` avec ça on peut se passé du `PostImageController`, l'idée est de pouvoir envoyer d'autres informations en plus de l'image et qu'il les ajoutent au niveau de notre entité donc on l'a fais via la déserialization du `multipart`
    > Si on a des effets de bord on.. branché à différents évènements `api-platform.com/docs/core/events/#the-event-system`
        > `ReadListener` va venir détecter les données depuis la couche de persistance et c'est lui qui appelle le `DataProvider` pour faire le reste de l'exécution
        > On a aussi des attributs qui permettent d'activer ou désactiver certaines opérations
            > `read` à false va désactiver le `ReadListener` qui revient à désactiver le `DataProvider`, utile si on a un controller qui vas récupérer les données de sa propre manière
            > `write` à false on aura plus la partie persistance, dans ce cas c'est au controller de le gérer
            > `deserialize` va désactiver la deserialization
            > `serialize` !! serialization
            > `validate` !! validation
        > !! des évènements qui sont envoyés par ApiPlatform, peut être utile pour envoyer des mails
            > `POST_WRITE` lorsqu'un nouvel article est crée..
    > !! veut le `endpoint` de `Category` seulement pour un `uri` et ne pas l'exposer, on peut créer un controller `EmptyResponse` qui retourne un reponse vide et si un utilisateur essaie d'acceder à cet url il sera vide
    > On peut utiliser les fichiers de déclaration `openApi` pour générer ceux de `typescript`
- 
- **GraphQL**
    > composer require webonyx/graphql-php : On aura accès à l'url `/api/graphql`
    > php bin/console api:graphql:export : Pour exporter la documentation graphql
    > php bin/console api:json-schema:generate : !! en `json`
    > php bin/console api:openapi:export : !! en `openapi`, `--yaml` pour le yaml

**Authentification - ApiPlatform**
- Pour indiquer l'authentification dans la documentation de `ApiPlatform` on a `OpenApiFactory`, pour plus d'infos voir la spécification de `openapi`
- Par `json` : Va utiliser le système de cookie qui est `statefull` vu que c'est le serveur qui va valider la requête et fonctionne si l'api est sur le même domaine que notre application
    > Après avoir créer l'utilisateur dans `security.yaml` - `main` - `json_login`, ensuite on crée `SecurityController` - `jsonLogin` et `LogoutListener` pour la déconnexion
    > Si on se connecte à `/login/json` en lui envoyant un `body json {"username": "admin", "password": "admin"}` on aura dans les `headers` un `Set-Cookie` qui va contenir le cookie correspondant à la session et là l'utilisateur est connecté, pour tester que l'authentification fonctionne on a `/fr/api/plateforme/me`

- !! `form html` : L'authenticator par défaut, si on n'a déjà un système d'authentification sur le site et qu'on veut mettre en place des api pour certains points d'entrée de notre application, donc on n'a pas besoin de connecté l'utilisteur via l'api
    > On utilise l'authentification par `form-login` de symfony et si l'utilisteur s'y connecte il aura un cookie et si l'api se situe sur le même domaine ça va fonctionner mais il faut mettre dans `config/routes/api_platform.yaml` - `stateless` à `false` qui permet l'usage normal de la session sinon on aura l'erreur `Session was used while the request was declared stateless`
    > Si on vas sur `/fr/api/plateforme/me` via api sans être connecté on sera redirigé vers le `login` vu qu'on utilise un authenticateur personnalisé `AppAuthenticator`, pour le relgé on a l'a fait dans sa méthode `start`

- !! `Apikey` : Le principe est que l'utilisateur va générer une clé d'api dans son compte et la clé lui permettra d'utiliser le site avec ses informations donc dans `User` on a `apiToken` ou `apiKey`, il devra envoyer une en tête `Authorization Bearer apiKey` avec sa clé dans une requête si la route est protégé par `APIKeyAuthenticator` - `security.yaml` l'authenticator personnalisé qu'on a crée

- !! `jwt` : Une authentification `stateless`, pour commencer on vas désengagé les cookies côté `insomnia` au clic droit sur la request et décoché `Send cookies au..`, `Store cookies..` voir `bin/` on peut dire à une route de ne pas utiliser les cookiees, on désactive le système d'authentification à partir du formulaire, le logout aussi le json dans `security.yaml`, on ne récupère pas l'utilisateur depuis une base de données il suffit de vérifier si la signature du token est valide pour pouvoir faire confiance aux données à l'intérieur, dans le cas de `ApiPlatform` l'utilisation de `jwt` repose sur le bundle `LexikJWTAuthentificationBundle` de symfony
    > composer require lexik/jwt-authentication-bundle
        > php bin/console lexik:jwt:generate-keypair : Permet de générer des clés nécessaires à la signature des jetons `jwt` et utilise `openssl` ainsi que d'autres extensions, sur `wsl` ou `linux`
            > Va générer des clés jwt dans `config/jwt`, une clé private qui permet de signé les choses et une publique qu'on pourra partager qui permet de valider si la signature est correcte
            > Pour générer les clés `jwt` 
                > php bin/console lexik:jwt:generate-keypair : Va `wsl` ou..
                > openssl genrsa -out private.pem 4096 .. openssl rsa -pubout -in private.pem -out public.pem
            > On ne versionne pas ces clés mais on génère à nouveau sur le serveur `.gitignore`
    > La configuration `config/packages/lexik_jwt_authentication.yaml`, ensuite dans `config/packages/security.yaml` on configure les `firewalls` et pour le `CORS` dans `config/packages/nelmio_cors.yaml`
    > Après configuration si on vas sur l'url `/api/login` ou `/api/login_check` intégré par défaut en `POST` avec les bons identifiants on reçois un `token` qui si on le copie au niveau de `jwt.io` on aura les infromations de l'utilisateur
    > Pour appeler `/me` en utilisant ce système on a documenté dans `OpenApiFactory` ensuite pour l'utiliser il faut l'en tête `Authorization Bearer token` le `token` reçu lors de la connexion
    > Vu que `jwt` va contenir les informations encodé de l'utilisateur on peut éviter qu'il fasse une requête à la base de données pour récupérer l'utilisateur, dans `config/packages/lexik_jwt_authentication.yaml` - `jwt` mais on l'active que pour la partie `/api` vu que la parti `/api/login` doit récupérer les informations depuis la base de données, après configuration si on appelle le `/me` on aura un tableau vide car il retourne une instance de `JWTUser` donc pour récupérer l'utilisateur notre `User` doit implémenter la `JWTUserInterface` qui a une méthode `createFromPayload`, il ne faut trop remplir les tokens d'informations car il peut être lourd
        > symfony.com/bundles/LexikJWTAuthenticationBundle/current/8-jwt-user-provider.html
    > On peut rajouter des informations dans le token grâce au système d'évènements, on a crée un subscriber `JWTSubscriber` et on s'est abonné au `lexik_jwt_authentication.on_jwt_created`
        > symfony.com/bundles/LexikJWTAuthenticationBundle/current/2-data-customization.html : Ou sur sa doc
    > Pour gérer l'expiration des `token` vu que par défaut il a une durée de vie de `1H` donc si en ex: on modifie la date d'expiration dans le `JWTSubscriber` et qu'on se rend sur `/me` on verra ça a bien expiré et on aura pas l'autorisation pour se connecté, dans le cas d'une vrai application ça veut dire que l'utilisateur doit se reconnecté ce qui n'est pas pratique donc on vas utilisé un système de `refreshToken` qui permet d'avoir un token qui ont une durée plus longue et vont être `statefull` et qui permettre de regénérer de nouveau token à la volée vu par défaut il n'y a moyen d'invalidé un `token jwt` ex: si quelqu'un génère un token puis supprime son compte son token va resté valide donc il ne faut pas choisir une date d'expiration qui trop loin dans le futur donc il faut un moyen pour le regénéré sans demandé à l'utilisateur de se reconnecté à chaque 10min, donc une des solution est le `refreshToken` qui permet d'avoir un token sauvegardé en base de données et qui va permettre de regénérer un nouveau token, on a le bundle `JWTRefreshTokenBundle`
        > composer require gesdinet/jwt-refresh-token-bundle : Puis on configure `routes.yaml` à partir de la doc
            > php bin/console doctrine:schema:update --force : Ou migré pour remplir la base de données, va crée une table `refresh_tokens`
        > Après configuration si on `/login` on aura un `token` et un `refresh_token` qui n'est pas un token `jwt` mais va être sauvegardé en base de données qu'on vas pouvoir utiliser pour regénérer un nouveau `token jwt` en tapent l'url `/token/refresh` avec un body json `refresh_token` qu'on a récupéré lors de la connexion, par défaut si on réutilise un `refresh_token` dans la base de données il va changé la date de validité à 1 mois après la date de regénération et on peut configurer
        > La configuration `config/packages/gesdinet_jwt_refresh_token.yaml`
            > `single_use` pour utiliser le refresh token qu'une seule fois `r`
        > Dans le cas ou on supprime un utilisateur on doit supprimer les tokens qui lui sont associés
            > php bin/console gesdinet:jwt:revoke `refesh_token` : Permet de révoquer un refresh token
            > php bin/console gesdinet:jwt:clear : Pour supprimer les tokens expirés de la base de données
        > On a des évènement pour `logout` qui permet d'invalidé un `token jwt`, pour le `refesh_token` on active le firewalls `security.yaml` - `firewalls` - `api` - `logout` puis `routes.yaml`
            > symfony.com/doc/current/security.html#logging-out

**Session**
- Pour utiliser un domaine différent pour contacter notre api
    > La configuration `config/packages/framework.yaml` - `session`
        > `cookie_samesite: none` pour indiquer le domaine de premier niveau n'est pas le même
        > `cookie_secure: true` !! le https
- Pour autoriser les requêtes `cross-domain` avec cookies 
    > composer require nelmio/cors-bundle : Pour installer le bundle `CORS`, installé par défaut
    > La configuration `config/packages/nelmio_cors.yaml`
        > `allow_credentials: true` pour autoriser l'envoi de cookie
        > `allow_origin: ..` !! les noms de domaine autorisés ex: `localhost:5173`


## Packages

**Fixtures** - Permet de remplir la base de données
- composer require --dev orm-fixtures
- php bin/console make:fixtures PostFixtures : Pour créer une fixture
- php bin/console doctrine:fixtures:load : !! remplir la base de données, `--env=prod` pour préciser un environnement
- Les fixtures se lance dans un ordre aplhabétique de `A-Z` avec les fichiers, pour utiliser les groupes on doit implémenté la `FixtureGroupInterface` et dans la commande ajouter le drapeau `-group-nom_group` et avoir une fonction `getGroupes` dans notre fixtures, doc
    > php bin/console doctrine:fixtures:load --group=recipes

- **Foundry** - Permet de créer des classes appelées `factory` pour chacune de nos entités pour automatiser leur création
    > composer require --dev zenstruck/foundry
    > php bin/console make:factory : Pour créer une factory
    > On peut définir la langue utilisée par `faker` en ajoutant dans `config/packages/zenstruck_foundry.yaml` - `faker` - `tab` - `locale: fr_FR`

**Vich/Uploader** - Permet de gérer l'upload de fichiers
- composer require vich/uploader-bundle
    > Va demandé si on veut executer la recettes [y] et crée les configurations à notre place
- La configuration `vich_uploader.yaml`
    > Pour créer un mapping pour un entité `vich_uploader.yaml` - `mappings`
    > !! changer la méthode de nommage des fichiers `vich_uploader.yaml` - `namer` 
- Pour qu'une entité utilise vich, dans `Recipe`
- Le helper dans la vue `vich_uploader_asset(recipe, 'thumbnailFile')` le nom du champ sur laquelle j'ai mis mon `uploadedField` et vas me donner le fichier
    > Pour avoir le chemin absolue d'une image dans un controller `$helper->asset($recipe, 'thumbnailFile')` dans `AdminRecipeController` - `edit`
- Pour que `vich` puisse appliquer sa logique il faut forcement qu'il y'est un `flush` qui se fasse, nous gràce au `POST_SUBMIT` dans notre `RecipeType`, si on modifie simplement le champ image symfony ne détecte pas qu'il y'a eu un changement et il ne persiste rien base de données, la solution est d'avoir un champ `updatedAt` et le remplir lors de l'édition
- Les événements de `vich`
    > `vich_uploader.pre_upload` déclenché avant que le fichier soit déplacé dans le répertoire final, utile si on veux modifier le fichier temporaire avant qu'il soit stocké
    > `vich_uploader.post_upload` !! après que le fichier a été déplacé dans le répertoire final
    > `vich_uploader.pre_remove` !! avant que le fichier soit supprimé
    > `vich_uploader.post_remove` !! après la suppression du fichier

**LiipImagineBundle** - Permet de redimensionner une image directement dans la vue
- composer require liip/imagine-bundle
- La configuration `config/packages/liip_imagine.yaml`
- Pour l'utiliser dans la vue on utilise le filtre `| imagine_filter('thumb')`, va prendre le chemin vers notre image et généré une autre en le gardant en mémoire
- Le problème est que lorsqu'on remplace ou supprime l'image original, ça ne le change pas au niveau du cache, on peut mettre un cron qui supprime les images qui ne sont pas appélé dans un certains temps ou le faire dans notre controller ou aussi avec un évènement doctrie dans `ImageCacheSubscriber` ou `AdminPropertyController` - `edit`
- php bin/console liip:imagine:cache:remove : Une commande pour supprimer le cache
    > php bin/console liip:imagine:cache:remove @thumb : !! supprimer le cache d'un filtre
- php bin/console liip:imagine:cache:warmup : Pour générer le cache à l'avance

**Intervention image symfony** - Un bundle officiel de `Intervention image` pour symfony
- composer require intervention/image-symfony
- La configuration `config/packages/intervention_image.yaml`, on peut y définir les options par défaut
- Pour l'utiliser on peut injecter `ImageManager`, dans `HomeController` - `image`

**KnpPaginator** - Permet de gérer la pagination, filtre..
- composer require knplabs/knp-paginator-bundle
- La configuration `config/packages/knp_pagniator.yaml`
    > doc.. on copie la configuration de `knp` pour symfony dans un fichier `knp.paginator.yaml` pour ensuite le configuré
- Pour l'utiliser dans la vue
    > `knp_pagination_sortable(recipes, 'ID', 'r.id')` pour le filtre
    > `knp_pagination_render(recipes)` pour la pagination
- Pour avoir le fichier de traduction, on crée un fichier `KnpPaginatorBundle.fr.yaml` dans `translations/` mais elle est faite automatiquement avec la commande de traduction de symfony

**Reset Password**
- composer require symfonycasts/reset-password-bundle
- php bin/console make:reset-password : Puis faire une migration
    > L'itinéraire vers laquelle les utilisateurs doivent ils être redirigés après la réinitialisation réussie de leur mot de passe
    > L'adresse email qui sera utilisée pour envoyer les confirmations de réinitialisation
    > Le nom associé à l'adresse email

**EasyAdmin** : Permet de générer une interface d'administration
- composer require easycorp/easyadmin-bundle
- php bin/console make:admin:dashboard : Pour créer l'espace administrateur
- php bin/console make:admin:crud Product : Pour créer un crud
- Le bundle utilise l'extension php `Intl` pour formater les dates et heures dans l'interface d'administration
- Un autre.. `SonataAdminBundle`


## Modules

**Glide** - Pour faire du redimentionnement d'image directement dans la vue `ImageController`, `ImagePathGenerator`..
- composer require league/glide league/glide-symfony
    > `SymfonyResponseFactory` permet de retourner une réponse compatibles avec `HttpFoundation`

**Auth OAuth** - Authentification via les réseaux sociaux
- composer require knpuniversity/oauth2-client-bundle
    > On vas se basé sur ce bundle et on vas utilisé le système d'authenticator sur symfony

- **Github**
    > Pour que l'utilisateur puisse accepter des permissions on n'a crée une app qui permet de demander l'accès des informations de l'utilisateur sur github - `github.com` - `settings` - `developers` - `OAuth Apps`
        > `Homepage URL` l'url de l'home page `http..localhost:8000/fr`
        > `Authorization callback URL` lorsque l'utilisateur va accepter les permissions, on vas le rediriger vers une pas de notre site, on peut mettre `http..localhost:8000/fr/oauth/check/github` avec l'idée d'avoir une url qui pourrait changer de service
    > D'autres résaux n'accepetent pas des url en local donc pour tester on peut utiliser des outils comme `ngrok` ou `localtunel` pour distribuer notre application à distance et avoir une url spécifique
    > On aura ensuite 2 informations importantes, le `Client ID` qui va être utilisé au niveau du front, peut être public et le `Client secrets` qui doit être secret
    > !! modifie l'entité `User` pour ajouter une propriété `githubId` qui sera l'id de l'utilisateur sur github
        > Lorsque que quelqu'un va se connecter sur github on récupère cet Id et ça nous permettra de retrouver l'utilisateur dans la base de données
    > !! install le package `knpuniversity/oauth2-client-bundle` - `y`, va crée un fichier de configuration `knpu_oauth2_client.yaml`, ensuite on peut installer des clients qui sont les différents réseaux sociaux qu'on veut utiliser, voir sur le dépôt gitbub `Configuring a Client`
        > composer require league/oauth2-github : Client github
            > Puis on configure ce client github avec les informations qu'on a récupérer lors de la création de l'app sur le site de github dans le fichier de configuration `config/packages/knpu_oauth2_client.yaml`
    > Après configuration, on n'a crée une route qui permet de rédirigé sur github `github_connect` dans `SecurityController`, ça nous demande d'autoriser et après il nous redirige vers notre site avec un code qui pourra être échangé contre un `access_token` qui permettra de récupérer des infos au près de github, sur la documentation du bundle il y'a 2 manière de faire les choses, une première est d'utiliser le controller et une deuxième est de créer un authenticator personnalisé `GithubAuthenticator` ensuite l'enregister dans `config/packages/security.yaml`
    > On peut tomber sur l'erreur `cURL error 60: SSL certificate problem: unable to get local issuer certificate` en local car il essaie de faire une requête https vers un serveur comme github et ne peut pas vérifier le certificat SSL
        > On télécharge le fichier `cacert.pem` sur `https..curl.se/ca/cacert.pem`
        > !! modifie `php.ini` pour indiquer le chemin
            > curl.cainfo = "C:\php\extras\ssl\cacert.pem"
            > openssl.cafile = "C:\php\extras\ssl\cacert.pem"

- **Google** - Dans le projet `meto`
    > composer require league/oauth2-google

**Géolocalisation** - Permet de déterminer la position géographique d'un objet, d'un appareil ou d'une personne sur terre grâçe à la latitude et la longitude
- On a plusieurs solutions pour intégrer ce système
    > Sol 1 : On peut présenter à l'administrateur au niveau de l'administration une carte ou il va pointer le bien
    > Sol 2 : !! dériver de l'adresse la latitude et la longitude, pour ça on n'a `Algolia places` qui permet de transformer un champ en système de géolocalisation pour faire de la recherche `npm install places.js`
        > Si on tape une adresse dans le champ il va nous proposé des suggestions et si on choisi on obtient des informations comme le code postal, la ville, la latitude et la longitude
- Pour charger une carte on a `Google Maps` ou `Leaflet.js` et `OpenStreetMap`
- Le site `gps-coordinates.net` permet de récupérer les coordonnées d'une destination
- Vu que `Algolia places` est déprécié on a des alternatives comme on a `placekit.io`, `mapbox.com`, `geocode.earth`, `mapsplatform.google.com/maps-products` ou `serpapi.com` qui contient plusieurs api
    > Le package `Algolia places` à été remplacé par `Algolia Autocomplete`, pour les clé api `dashboard.algolia.com` - `settings` - `api keys`
        > npm install algoliasearch
        > npm install @algolia/autocomplete-theme-classic
        > npm install @algolia/autocomplete-js
    > !! `Placekit - Address Autocomplete` mais demande le HTTPS pour la clé publique 
        > npm install --save @placekit/autocomplete-js
            > npm install --save @placekit/autocomplete-react : Pour react
            > npm install --save @placekit/client-js : Permet d'utiliser l'api côté serveur et aussi la clé privée

- Pour faire le filtre dépuis la `lat` et `lng` on doit calculer la distance entre ces 2 points, pour ça on peut rechercher `distance lat lng mysql` sur internet et on aura la requête qui permet de le faire
    > Dans le calcule de la distance on peut tomber sur l'erreur `[Syntax Error] line 0, col 73: Error: Expected known function, got 'ASIN'` car par défaut dans le `dql` de doctrine ne supporte pas les fonctions `ASIN` ou autre, donc pour les ajoutés on peut chercher dans le navigateur `doctrineextensions` et on n'aura des librairies comme `composer require beberlei/doctrineextensions` qui va ajouter plusieurs fonctions, ensuite dans la documentation on explique comment les rajoutés dans symfony dans le fichier `config/packages/doctrine.yaml` - `doctrine` - `orm` - `dql`

- **OpenStreetMap - Leaflet.js** `leafletjs.com` - Une carte du monde libre
    > npm install leaflet : Un package spécialisé dans la création de cartes interactives pour le web
    > Pour changer de damier ou fournisseur de carte on peut taper `Open street map tiles` sur internet et on devrait tomber sur leur `wiki` sur lequel on nous donne des fournisseurs de carte comme `maps.stamen.com`
        > On a aussi `openmaptiles.org`, `www.maptiler.com` ou `apidocs.geoapify.com` qui utilise une clé d'api, donne Map tiles, Marker Icon API, Address Autocomplete, Places API..
        > `leaflet-extras.github.io` pour des exemples de layer existant
        > Dans le cas de `Mapbox` un fournisseur de carte mais payant avec un niveau gratuit qui donne pas mal d'affichage et qui fonctionne avec un `accessToken` donc aller sur le site et créer un compte pour l'avoir
    > Pour l'apparence de la carte, on peut utiliser le `tileSet` de `mapbox` qui nécéssite un `access_token` au lieu de celui de `OpenStreetMap`
    > Par défaut `leaflet` ne fourni que le `lat` et `lng` dans l'évènement `map.on('click')` vu qu'il ne contient pas de base de données des villes, adresses, rues.. contrairement à `algolia places`, donc pour contourner ce problème on peut utiliser l'api `Nominatim` de `OpenStreetMap` ou `Photon` de `Komoot`.. pour faire du `reverse geocoding`
    > Plugins
        > Le plugin `Leaflet Control Geocoder` permet d'ajouter une barre de recherche avec autocomplétion, utilise le geocoder `Nominatim` par défaut et basé sur `OpenStreetMap`, on peut aussi configurer un autre fournisseur de géocodag comme `mapbox` ou `photon`
            > npm install leaflet-control-geocoder
        > !! `Leaflet Routing Machine` permet d'ajouter le calcul et l'affichage d'itinéraires sur la carte

- **Google Maps** - Payant
    > Google Maps Api - Documentation - API Maps JavaScript : On crée un projet ensuite on n'aura le `apiKey`
    > Le site `snazzy map` permet de styliser notre map, prendre un style sur le site et l'ajouter dans l'option `styles` de notre objet `map`

**Notification instantanée - Mercure Component** - Permet à notre serveur d'envoyer des notifications en temps réel qui vont directement être prise en compte par le navigateur `mercure.rocks`
- Se repose sur le protocol `mercure` qui n'est pas basé sur les `websockets` mais `SSE` Server Sent Event qui fonctionne à travers un système de `Hub` qui va centralisé les informations et qui va se charger d'envoyer les notifications
    > Pour comprendre comment fonctionne ce `Hub` écris en `Go` sans symfony, on vas dans le dépot github de `dunglas mercure`, release et on télécharge la version qui correspond à notre système d'exploitation `bin/mercure/mercure.exe`
    > !! lancer le serveur on doit définir des variables d'env ou paramètres dans le cas de windows
        > JWT_KEY='adamo' ADDR='localhost:3000' ALLOW_ANONYMOUS=1 ./mercure.exe : Sur linux
        > $env:JWT_KEY='eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdfX0'; $env:ADDR='localhost:3000'; $env:DEMO='1'; $env:ALLOW_ANONYMOUS='1'; $env:CORS_ALLOWED_ORIGINS='*'; $env:PUBLISH_ALLOWED_ORIGINS='http://localhost:3000'; ./mercure : Sur windows
            > `JWT_KEY` la clé d'authentification pour signé le token jwt
            > `ADDR` le port sur lequel on installe le serveur mercure
            > `ALLOW_ANONYMOUS` permet des connexions anonymes ce qui va permettre d'envoyer des notifications globales
            > `DEMO` permet d'activer le mode debug
            > `CORS_ALLOWED_ORIGINS` les domaines qui sont autorisés à intérogé et s'inscrire sur le serveur mercure pour reçevoir les notification, `*` n'importe quel site à condition d'avoir le token d'authentification
            > `PUBLISH_ALLOWED_ORIGINS` qui a le droit d'envoyer des notifications par l'intermédaire du serveur
        > Pour installer via docker
        ```conf
        docker run -d -p 80:80 -p 443:443 \
        -e MERCURE_PUBLISHER_JWT_KEY='!ChangeMe!' \
        -e MERCURE_SUBSCRIBER_JWT_KEY='!ChangeMe!' \
        -e MERCURE_EXTRA_DIRECTIVES='cors_origins *' \
        dunglas/mercure
        ```
    > On se rend sur `localhost:3000` et on verra la page d'acceuil de mercure
    > Pour tester le principe du protocol, on publie un évènement sur `localhost:3000/.well-known/mercure` en lui envoyant des informations et doit être appelé en `POST`
        > `Token Bearer` ce token doit être au format `jwt` qui pour le générer `jwt.io` dans lequel notre clé secret est celui qu'on n'a mis au niveau du terminal et on met ce token dans le `Bearer`
            > On utilise le format suivant pour générer le token jwt sur `jwt.io` - `Decoded Payload` ensuite `Secret` pour la clé secret
            ```conf
            {
                "mercure": {
                    "publish": [] ; -- Pour indiquer sur quel canaux on n'a le droit de publier, si on ne met rien c'est qu'on met sur le canal anonyme et si on met ["*"] alors on publie sur tous même les canaux privées
                }
            }
            ```
    > Pour envoyer un message à mercure qui sera retransmis à tous les clients qui écoutent au niveau du `Hub` il faut au niveau du `Body` de la requête envoyés des informations en `Form URL Encoded`
        > `topic ping` le topic, normalement doit être une `url` valide car il servira au écouteur à s'abonner à cet évènement `http://monsite.com/ping`
        > `data 0` les données qu'on veut envoyés
    > Après avoir soumis la requêtes et que le serveur nous repond on verra au niveau du terminal un évènement qui a été publié `Update published` ensuite l'id de cet évènement `update.id=urn:uuid:019..` 
    > Ensuite après l'envoi de l'évènement, on peut écouter côté navigateur pour pouvoir réagir `home/index.html.twig` - `block javascripts`
    > Pour tester on envoi une requête à l'url `localhost:3000/.well-known/mercure`, on peut avoir l'erreur `CORS` côté navigateur parcequ'on est sur `localhost:8000` et qu'on essai d'appeler `localhost:3000` donc notre serveur symfony et mercure ne sont pas sur le même nom de domaine, pour régler on le fais lors du lancement du serveur merucre
        > CORS_ALLOWED_ORIGINS=http://localhost:8000 JWT_KEY='aVerySecret' ADDR='localhost:3000' ALLOW_ANONYMOUS=1 ./mercure.exe

- Pour l'utiliser avec `symfony`
    > composer require mercure
    > La configuration `config/packages/mercure.yaml`
        > Dans le `.env` on aura des variables `MERCURE..` qu'on a redéfini dans notre `.env.local`
    > Pour publier un évènement `HomeController` - `ping`, dans `home/index.html.twig` on n'a un formulaire pour pingé un ou plusieurs utilisateurs
    > !! écouter côté navigateur pour pouvoir réagir `home/index.html.twig`
    > On doit authentifier un utilisateur lorsqu'il se connecte à notre `Hub` dans le cas ou on envoi sur un canal privé, mais n'est pas obligatoire dans `symfony` pour la partie cookie car il envoi le `set-cookie` automatiquement
        > Sol 1 : Dans le cas d'un navigateur on peut utiliser l'authentification par `set-cookie` en créant un cookie `mercureAuthorization` qui doit contenir les tokens jwt qui liste les autorisations, pour le générer et envoyer au navigateur on a une librairie `composer require lcobucci/jwt`, dans `MercureCookieGenerator` et utiliser dans `HomeController` - `index`
            > On a ensuite modifié le javascript `EventSource` - `withCredentials`.. on est obligé d'activer l'envoi des  `credentials` si le hub est sur un domaine différent
        > Sol 2 : L'authentification par en-tête `Authorization: Bearer ..`, recommandé pour les `SPA` ou mobile..
            > !! javascript `EventSource` - `headers`..

- On peut l'intégrer avec `Api Platform` pour publier automatiquement une mise à jour sur mercure à chaque modification d'une ressource, doc

**Tag twig personnalisé** - Dans `TwigCacheExtension` - `Cache/`
- La clé doit implémenté le `CachableInterface`

**Captcha personnalisé**
- Le principe du `captcha` est que côté serveur avant de générer un formulaire on vas générer une clé qui va representé le problème qu'on vas soumettre à l'utilisateur (dans notre on va généré une position qui celle attendue de la pièce de puzzle), on vas associé à ce code une clé qui est la solution au problème qu'on vas envoyé dans le formulaire, le client reçoit cette clé et l'envoyé à un service de `captcha` qui à partir de la clé va être capable de générer le problème qui représente la solution qui est attendue de la part de l'utilisateur humain et le renvoyé à l'utilisateur (dans notre cas ça va être une image qui va contenir notre pièce de puzzle qui va être manquente dans notre élément) ensuite l'utilisateur va resoudre le problème et généré notre solution et ensuite il va soumettre le formulaire avec la clé qui représente notre problème et la solution que lui il a donné, côté serveur à partir de cette clé on vas être capable de voir la solution qui est attendue et la comparé avec celle qui est envoyé par l'utilisateur, si les deux correspondent alors c'est un humain
- L'avantage est que vu que c'est une solution personnalisé si quelqu'un veut attaquer notre captcha va devoir mettre en place un robot spécifiquement pour nous
- Dans `Domain/Antispam/`
    > Le `ChallengeInterface` permet de générer une clé et le problème associé
    > !! `ChallengeGenerator` !! le problème et renvera une reponse qui permet de soumettre le problème
- Dans la génération d'image `PuzzleGenerator` on peut avoir l'erreur `Unable to decode input` qui signifie qu'il n'arrive pas à lire l'image
- On a testé dans `RegistrationController` - `register`
- !! crée `CaptchaController` pour cabler la génération du problème
- Dans `Captcha.js` on a crée le code javascript pour afficher le `captcha` et `captcha.scss` pour le style
- On a crée un validateur `ChallengeValidator` et `Challenge` pour vérifier la valeur de clé côté serveur
- Dans le cas de symfony on a généré un champ `CaptchaType` qui permet de générer le `captcha` qu'on a utilisé dans `RegistrationFormType` et on a généré la partie html qui correspond à ce champ avec ce thème `form.html.twig`
- Pour l'amélioration on peut faire en sorte que le fond change avec le temps, on peut aussi bougé les pièces..

**Bundle personnalisé** - Permet de spérarer le code de notre application pour être utiliser de projet en projet
- On étend de `Bundle` et pour le charger on le met dans `config/bundles.php`
- Pour modifier le format html il faut créer une nouvelle vue twig en respectant une convention `Resources/views`, rien qu'en créant cette vue on aura un nouveau namespace au niveau de twig `php bin/console debug:twig` - `@Recaptcha` qui prefixera le chemin de notre vue
- !! que la vue fonctionne il faut rajouter dans les resources de twig notre nouveau chemin en modifiant notre container dans le `RecaptchaBundle`
    > php bin/console debug:container --parameters | grep twig : Pour avoir les paramètres qui correspondent à twig
    > php bin/console debug:container --parameter=twig.form.resources : Va contenir l'ensemble des resources et chemin dans lequel le système de formulaire va chercher des templates
- !! avoir une configuration qui soit plus simple à utiliser, on a d'abord créer le fichier `recaptcha.yaml`, ensuite il faut suivre la convention `DependencyInjection/`
- !! valider la reponse du captcha on a utiliser une contrainte `Constraints/` et on utilise la librairie `composer require google/recaptcha`, ce package est déjà disponible dans les recipes symfony, va nous demander si on veut configurer les recettes ce qui va créer un fichier `recaptcha.yaml` qui configura le service google recaptcha, mais on va repondre [n] car on veut que c'est le bundle qui définissent comment il doit fonctionner
- !! plus d'informations sur recaptcha `developers.google.com/recaptcha/docs/invisible`

**Google reCAPTCHA** - Pour créer des clés `reCAPTCHA` on vas sur `Google reCAPTCHA Admin`
- composer require karser/karser-recaptcha3-bundle : Pour intégrer `reCAPTCHA v3` qui fonctionne sans affichage explicite d'un challenge visuel mais évalue son comportement en arrière plan, il analyse le comportement du visiteur et renvoie une note entre `0.0` comportement suspect bot, `1.0` comportement humain
    > La configuration `config/packages/karser_recaptcha3.yaml`
        > On ajoute son `formType` dans notre formulaire
        > !! peut charger le javascript de reCAPTCHA si non chargé
- composer require victor-prdh/recaptcha-bundle : Pour intégrer `reCAPTCHA v2` avec l'option de case à cocher `Je ne suis pas un robot`
    > La configuration `config/packages/recaptcha.yaml`
- composer require excelwebzone/recaptcha-bundle : Pour le support de `reCAPTCHA v2` !!
    > !! `config/packages/ewz_recaptcha.yaml`

**Swagger - OpenApi** - Une documentation d'api
- Le site `editor.swagger.io` permet de générer et de travailler sur une spécification `openapi`
- Pour qu'il génère la documentation des spécification on.. la partie `Swagger Inspector` on lui donne l'url de l'api
- La librairie php pour swagger `swagger-php` permet de générer une documentation à travers des annotations qui doivent correspondre au spécification `openapi` qui est sur la documentation de swagger, on a créer un fichier `swagger/swagger.php` qui permet de mettre les informations qui sont globalement utilisé par la spécification swagger
    > composer require zircote/swagger-php : Pour.. et on aura un outil qui permet de générer le `json` ou `yaml`
        > ./vendor/bin/openapi --format json --output ./swagger/swagger.json ./swagger/swagger.php src : On lui donne le format, le nom du fichier à générer et le fichier à parser et explorer tous ce qui se trouve dans le dossier `src`
        > ./vendor/bin/openapi --format yaml --output ./swagger/swagger.yaml ./swagger/swagger.php src : Pour du `yaml`, on peut même le copier et coller sur le site `editor.swagger.io` pour voir la documentation
    > Pour éviter de faire les copier coller, on a un outil `swagger ui` qui est une interface visuel qui permet de gérer les fichiers `json` de swagger et de gérer une interface, on télécharge le `zip` dans ses release de `github` on copie le dossier `zip` au niveau de notre projet `public/swagger` on pourra y acceder dans l'url `http..localhost:8000/swagger/index.html`
        > Pour générer le fichier `json` pour `swagger ui` on fais et on modifie le fichier `index.html` pour lui indiquer le fichier `json` :
            > ./vendor/bin/openapi --format json --output ./public/swagger/swagger.json ./swagger/swagger.php src

- **NelmioApiDocBundle** - Permet de générer la documentation openapi en `json` ou `yaml`
    > php bin/console api:doc:dump --format=json > openapi.json : Permet d'exporter la documentation
    > php bin/console api:doc:dump --format=yaml > openapi.yaml : && au format `yaml`
    > La configuration `nelmio_api_doc.yaml`


## Front

**Assets Mapper**
- php bin/console asset-map:compile : Pour build les ressources
    > Pour l'utiliser en `dev` on doit installer la commande de `Symfony` ensuite dans le terminal `symfony serve` va charger les ressources

**Webpack Encore**
- composer require symfony/webpack-encore-bundle : Ensuite `npm install`
- La configuration `webpack.config.js`
- Dans le `base.html.twig`
    > `encore_entry_link_tags('app')` pour charger le css
    > `encore_entry_script_tags('app')` !! le js
- npm run dev | npm run watch | npm run build : Les commandes charger les ressources
- npm run dev-server : Pour ouvrir un serveur de développement et avoir du `hot reload`
    > npm install webpack-dev-server --save-dev

- Pour utiliser `turbo` avec `webpackEncore`
    > composer remove symfony/ux-turbo symfony/asset-mapper symfony/stimulus-bundle
    > composer require symfony/webpack-encore-bundle symfony/ux-turbo symfony/stimulus-bundle : Puis `npm install --force`
    > Dans `webpack.config.js` - `.enableStimulusBridge('./assets/controllers.json')` mais automatique

- **Sass**
    > npm install sass-loader@^16.0.1 sass --save-dev : Dans `webpack.config.js` on.. `.enableSassLoader()`

- **React**
    > npm install react react-dom --save
    > npm install @babel/preset-react@^7.9.0 --save-dev : !! `.enableReactPreset()`

- **Vue**
    > npm install vue@^3.2.14 vue-loader@^17.0.0 @vue/compiler-sfc --save-dev : !! `enableVueLoader()`
        > `vue-loader` permet de charger les fichiers `.vue` dans webpack
        > `vue-style-loader` !! injecter dynamiquement les styles css contenus dans les composants `.vue` dans le dom pendant le développement ce qui permet le rechargement à chaud `HMR`

- **Preact**
    > npm install @babel/plugin-transform-react-jsx@^7.12.11 --save-dev : !! `.enablePreactPreset()`
    > npm install preact 
        > On a aussi le `@prefresh/webpack` qui permet de gérer le `HMR` mais on a déjà le `dev-server` de webpack

- **TypeScript** dans `Webpack Encore`
    > npm install ts-loader@^9.0.0 --save-dev : !! `.enableTypeScriptLoader()` et on configure `tsconfig.json`

- **Babel**
    > Dans `Webpack Encore` on a pas besoin d'installer `babel-loader` manuellement car c'est une dépendance interne
    > npm install --save-dev @babel/core @babel/preset-env : .. `.configureBabel()` qui permet de configurer `babel` et `.configureBabelPresetEnv()` !! les polyfills `@babel/preset-env`
    > Pour la configuration on a 2 solutions
        > Sol 1 : On peut faire une configuration `babel` interne dans `webpack.config.js` - `.configureBabel()`
        > Sol 2 : !! externe grâce au fichier `.babelrc` ou `babel.config.js`
    > Les packages `core-js` et `regenerator-runtime` ajoutent des polyfills pour les fonctions modernes comme `Promise` ou `async/await`..
    > npm install @babel/plugin-transform-react-jsx@^7.12.11 --save-dev : Permet de transformer le `jsx` en javascript standard compréhensible par le navigateur, mais `Webpack Encore` l'installe et le configure automatiquement grâce à `.enableReactPreset()`

- **Postcss**
    > npm install postcss-loader@^8.1.0 --save-dev : !! `.enablePostCssLoader()`
    > La configuration `postcss.config.js`
    > On peut installer `npm install autoprefixer --save-dev` mais `postcss` l'utilise en interne
    > Plugins
        > `postcss-preset-env` permet d'utiliser des fonctionnalités css modernes avant qu'elles ne soient prises en charge par tous les navigateurs un peu comme babel
            > npm install postcss-preset-env --save-dev

**Symfony UX** `ux.symfony.com`
- Le fichier `controllers.json` contient l'ensemble des controllers stimulus à charger
    > `"fecth": "eager"` indique que le controller va être chargé de suite
    > `"fecth": "lazy"` ne sera charger que lorsqu'il est requis

- **Ux Turbo** - Permet d'améliorer les performances d'une application web en remplaçant uniquement certaines parties
    > composer require symfony/ux-turbo
    > On a 3 grandes parties dans `turbo`
        > `turbo drive` pour avoir une navigation fluide
        > `turbo frames` !! modifier qu'une partie de la page
            > Dans `admin/recipes/index.html.twig` - `turbo-frame` puis `..edit.html.twig`
        > `turbo stream` !! permet de préciser des opérations à faire ou les mises à jour en temps réel
            > Dans `AdminRecipeController` - `remove` puis `.._delete.html.twig`, aussi `..index.html.twig` - `tr id=..` et `id=flash` pour le flash

    > Les métadonnées
        > <meta name="turbo-visit-control" content="reload"> pour désactiver turbo drive pour une page
        > <script data-turbo-eval="true"></script> !! forcer la réévaluation du javascript car turbo ne recharge pas le javascript à chaque navigation

    > Les attributs
        > `data-turbo-cache='false'` permet de désactiver le cache pour une page ce qui force turbo à faire une vraie requête http et non une version en cache
        > `data-turbo='false'` !! le cache seulement pour une requête de formulaire ou un lien et recharge la page de manière classique
        >` data-turbo-action='replace'` pour forcer turbo à remplacer la page au lieu de la visiter
        > `data-turbo-track='reload'` !! le rechargement complet si le fichier change
        > `data-turbo-prefetch` !! activer le préchargement des pages au survol et permet d'accélérer la navigation
        > `data-turbo-confirm='Êtes-vous sûr ?'` permet d'afficher une confirmation avant l'envoi d'un formulaire
        > `data-turbo-frame='nom_du_frame'` !! d'envoyer le résultat dans un `turbo-frame` spécifique

    > Les évènements
        > `turbo:load` remplace `DOMContentLoaded` et exécute du javascript après chaque navigation
        > `turbo:before-cache` !! néttoyer le cache avant que turbo mette une page en cache, aussi de stopper les animations, détacher les observateurs et on l'utilise si turbo drive est en mode cache
        > `turbo:before-render` !! intercepter avant que le `dom` soit remplacé, aussi de détruire les instances
        > `turbo:frame-load` !! exécuter du javascript après chargement d'un `turbo-frame`
        > `turbo:render` après le rendu du nouveau contenu
        > `turbo:submit-start` lorsqu'un formulaire est soumis
        > `turbo:submit-end` après la soumission du formulaire
        > `turbo:before-fetch-request` avant l'envoi d'une requête fetch et on peut ajouter des headers custom

    > Dans le javascript
        > `Turbo.clearCache()` pour vider tout le cache turbo
        > `Turbo.visit('/dashboard', { action: 'replace' })` !! forcer un rechargement complet dans le javascript
        > `Turbo.renderStreamMessage(html)` !! rendre un message stream
        > `Turbo.session.drive = false` !! désactiver la navigation turbo globalement 

    > Les script inline dans les vues ne se réexécutent pas, si on ajoute du code javascript directement dans notre vue `turbo` ne l'exécute qu'une seule fois et pas après navigation ou remplaçement de fragment
    > Dans l'utilisation de `turbo` il faut éviter de rendre le formulaire avec la méthode `$form->createView()`
    > On peut avoir l'erreur `Uncaught TypeError: Cannot read properties of undefined (reading 'trim')` vu que `turbo` ne recharge pas toujours le `dom` entièrement il est remplacé ou mis à jour partiellement, si on ne détruis pas les objets javascript créés sur les anciennes pages il restera des écouteurs d'événements des timers ou intervals qui continuent à tourner, des références d'objets gardées en mémoire par le garbage collector, des observateurs non déconnectés ce qui cause des problèmes comme la saturation progressivement de la mémoire, le code s'exécute plusieurs fois sans raison
        > L'erreur provient lors d'un retour en arrière vu `turbo` gère son cache et restaure le `dom` à partir d'un snapshot donc le code du `turbo:load` est relancé, mais ex: les anciens `tomselect` déjà initialisés sont aussi restaurés et `tomselect` essaie de réinitialiser un `select` déjà décoré ce qui casse `trim` sur une propriété indéfinie, pour corriger on.. dans les évènements `turbo:before-cache` ou `turbo:before-render`

- **Ux Autocomplete** : Permet de générer des select avec un système de recherche
    > La configuration `ux_autocomplete`
    > Après l'installation on peut ajouter un nouvel attribut sur nos `EntityType` - `['autocomplete' => true]` et on aura un select personnalisé qui a en plus un système de recherche mais n'est pas basé sur de l'ajax
    > Pour changer le `css` utilisé par `auto..` dans `controllers.json` - `autocomplete` - `autoimport` on met à `true` celui qu'on utilise ex: bootstrap puis..
        > php bin/console importmap:require tom-select/dist/css/tom-select.bootstrap5.css
    > Pour avoir un select qui va recherché la liste en ajax si on a plusieurs enregistrements
        > php bin/console make:autocomplete-field : Puis l'entité qu'on veut rechercher et on aura un select qui va rechercher en ajax en appelant une url, dans `RecipeFormType` on.. `CategoryAutocompleteField`

- **Ux Toggle password**
    > Pour l'utiliser.. un `PasswordType` on.. l'option `toggle => true`, on.. hidden_label => Masquer, visible_label => Afficher, visible_icon => null, hidden_icon => null, button_classes => [], toggle_container_classes => []

- **Ux React**
    > composer require symfony/ux-react
        > Va modifier `app.js` pour ajouter la recette flex `registerReactControllerComponents` qui charge les composants de contrôleur react destinés à être rendus par `twig`, les fichiers sont dans `assets/react/controllers` et seront rendus sous forme de composants react
        > Pour le rendu dans `twig` on n'a `react_component()`, doc
    > Pour le support du `tsx` on doit avoir le fichier `tsconfig.json` et activer le préset `typescript`
    > On n'a pas besoin d'installer les dépendances `@types/react` ou `@types/react-dom` avec react ux
    > !! peut marquer un composant permanent `react_component('Hello', {fullName: 'Fabien'}, {permanent: true})`, doc

- **Ux Toolkit** : Permet d'utiliser des composants `twig` pour créer son propre design système
    > composer require --dev symfony/ux-toolkit : Puis doc..
        > Il utilise `tailwind`
            > tailwindcss.com/docs/installation/framework-guides/symfony
        > php bin/console ux:install button : Pour installer un kit `templates/components`

**Shadcn**
- Pour l'instaler on choisi l'installation manuel
    > ui.shadcn.com/docs/installation/manual
- !! indiquer le chemin des fichiers ou dossier `components.json` - `aliases` là ou il va générer les composants
- !! `npm install radix-ui`

**Stimulus** - Un micro framework javascript conçu pour ajouter du comportement interactif dans les applications basées sur du html côté serveur et ne prend pas le contrôle total du dom `hello_controller.js`
- Pour l'utiliser on crée un `Controller` stimulus dans `assets/controllers` et on le relie dans le html via des attributs `data-*`
    > ex: `data-controller="hello"` le nom du controller, `data-action="click->hello#greet"` l'action à appelé
- Pour l'utiliser avec `symfony`
    > composer require symfony/ux-stimulus
- L'avantage de son utilisation avec `symfony` est qu'il permet de se passé des frameworks comme `react` ou `vue` et est compatible avec le `SSR` vu que le contenu vient du serveur

**Vite dans symfony**
- On crée un nouveau projet vite qui a démarrer un serveur de developpement sur le port `5173`, si on regarde dans le code source de la page on aura des `script,refresh,@vite/client` donc techniquement on pourrait les copiés dans notre `base.html.twig` et ça va fonctionné, pour l'utiliser au niveau symfony on a déplacé le fichier `vite.config.js` au niveau de symfony, pour le `package.json` on a installé les dépendances de vite dans notre projet, pour le `src` on a déplacé ces fichiers dans `assets/vite`
    > Pour faire maintenant un `run dev` on a modifié la configuration `vite.config.js`
- Pour ne pas avoir à mettre les scripts dans le `base.html.twig` on a crée un helper twig `vite_asset()`
- !! les images vu qu'ils étaient chargé depuis vite et que maintenant on est dans notre symfony, il va les chargés depuis notre application symfony mais ne sera pas servir car il ne le connaît pas, une solution est de créer un lien symbolique
    > ln -s ../assets assets : Va avoir l'impression de servir l'image sans soucis
- !! avoir le `hot reload` quand on modifie un fichier `twig` dans `vite.config.js`
- !! affiner la partie `cache` on a ajouter un pool dans `config/packages/cache.yaml`
    > php bin/console cache:pool:clear vite_cache_pool : Si on ne veut vider que notre pool sans vidé le cache de l'app
- Si on fais un nouveau `build` on doit penser à vider le `cache` car on a mis le `default_lifetime` à 0









composer require zenstruck/messenger-monitor-bundle

 zenstruck/messenger-monitor-bundle  instructions:

* You're almost ready to use zenstruck/messenger-monitor-bundle!

* First, create a migration with symfony console make:migration
  and run it with symfony console doctrine:migrations:migrate.

* Visit /admin/messenger as a user with ROLE_ADMIN
  to see the Dashboard UI.

* Read the documentation at https://github.com/zenstruck/messenger-monitor-bundle

Found 72 security vulnerability advisories affecting 24 packages.
Run "composer audit" for a full list of advisories.
Using version ^0.6.0 for zenstruck/messenger-monitor-bundle






ApiPlatform :
Le composant `Mcp` (exposer nos api aux agents ia) permet à nos ressources d'être directement consommé par les ia, 


Le projet est en **4.2.15**. L'écart réel passe donc par 4.3 et 4.4. Lisons les sources.

Voyons ce que cela coûte **ici**, concrètement.

Vous êtes en **4.2.15**, et la 5.0 est sortie (patches jusqu'à 5.0.4). L'écart n'est donc pas « 4.2 → 5 » mais **trois versions** : 4.3, 4.4 et 5.0. J'ai mesuré ce que chacune coûte ici plutôt que de recopier les notes de version.

## Aucun blocage de prérequis

5.0 exige **PHP ≥ 8.2** et **Symfony ^7.4 || ^8.0**. Vous êtes en PHP 8.4 (8.5 à l'exécution) et Symfony 8.0. La porte est ouverte — c'est souvent là que ça coince, pas ici.

## Ce qui change vraiment pour vous

**Le vrai coût : 63 `#[ApiFilter]`.** Dépréciés en 4.4 au profit de `QueryParameter`, supprimés seulement en **6.0**. Le détail : 26 `SearchFilter`, 21 `OrderFilter`, 14 Boolean/Numeric/Exists/Date/Range/Enum, plus un filtre maison qui étend `AbstractFilter` (`PersonnelFilter`) — la classe de base est dépréciée elle aussi. Une commande `api:upgrade-filter` fait la conversion, mais sur 63 déclarations elle demandera une relecture : ma mémoire du projet garde le souvenir d'un filtre non déclaré qui répondait 200 avec la liste entière, en silence. C'est exactement la famille de défaut qu'une conversion automatique peut réintroduire.

**Une rupture en 4.3 qui vous touche directement** : les filtres Doctrine à paramètres exigent désormais un `property` explicite, sinon `InvalidArgumentException`. **61 de vos 63 déclarations ont déjà `properties:`** — deux ne l'ont pas, à vérifier. Autant dire rien.

**Le passage des erreurs de dénormalisation de 400 à 422** (5.0, avec payload `ConstraintViolation`) est le changement le plus visible côté clients. **Vos deux clients sont déjà prêts** : l'app Flutter teste `statusCode == 400 || statusCode == 422`, et le front sait lire les violations par champ. Ce serait même un gain — les messages tomberaient sous les champs au lieu d'un bandeau global.

## Ce qui ne vous concerne pas

Le changement le plus bruyant de la 5.0 — `jsonapi.use_iri_as_id` qui passe à `false` — **ne vous touche pas** : vos formats sont `jsonld`, `json` et `multipart`, pas JSON:API.

Trois options supprimées (`enable_link_security`, `resource_class_directories`, `validator.query_parameter_validation`) **n'apparaissent nulle part dans votre configuration active** — seulement dans `config/reference.php`, qui est le fichier de référence généré.

`ApiTestCase` part dans un paquet séparé `api-platform/test` : **le vôtre étend `WebTestCase` de Symfony**, pas celui d'API Platform. Vos 365 tests API ne bougent pas.

Et **zéro entité en lecture seule**, donc la rupture 4.3 sur PUT/PATCH des entités `readOnly` ne vous atteint pas.

## Ce que vous gagneriez

Un gain qui vous est propre, en **4.3** : *les expressions `isGranted` sont évaluées avant le provider*. C'est exactement l'inverse du piège que vous aviez dû contourner — `MeProvider` et `CaisseScopeExtension` refusent l'utilisateur nul eux-mêmes pour éviter un 500 au lieu d'un 401. Ce contournement deviendrait inutile.

Les autres, par ordre d'intérêt pour ce projet :

- **`throwOnNotFound`** (4.4) : décider par opération si un `null` du provider rend 404. Vous avez plusieurs providers qui lancent `NotFoundHttpException` à la main.
- **`repositoryMethod`** (4.4) : une opération pointe vers une méthode de repository **en gardant filtrage et pagination**. Vous avez beaucoup de contrôleurs qui interrogent les repositories en direct, justement pour sortir du périmètre — ceci pourrait en simplifier une partie.
- **`routePriority`** (5.0) : l'ordre de correspondance des routes. C'est ce qui aurait réglé `/api/permissions/catalogue` capturé par `/api/permissions/{id}` ; vous le tenez aujourd'hui par `requirements: ['id' => '\d+']`, présent **156 fois**. Le patron actuel marche, je ne le changerais pas.
- Filtres composables (`OrFilter`, `ComparisonFilter` avec `between`, `ChainFilter`, `FreeTextQueryFilter`), **OpenAPI 3.2**, Scalar en plus de Swagger UI, `hydra:memberAssertion`, méthode HTTP QUERY, et un composant **MCP** (4.3) pour exposer l'API à des agents.

## Ce que j'en ferais

Pas de bond direct. **4.2 → 4.3 → 4.4**, en traitant les dépréciations à chaque palier, puis 5.0 qui n'est alors presque plus qu'un retrait de code mort. Vos 586 tests sont ce qui rend cette montée raisonnable : c'est le filet qui dira ce que les notes de version ne disent pas.

**Sources :** [changelog officiel](https://api-platform.com/docs/changelog/), [le billet de soyuka sur 4.4 et 5.0](https://soyuka.me/api-platform-4-4-and-5-0/), [composer.json de api-platform/core](https://github.com/api-platform/core/blob/main/composer.json), [release v5.0.0](https://github.com/api-platform/core/releases/tag/v5.0.0).







Docker, ia, UI, Symfony, backups et backfill

- `Symfony Language Tools` le serveur officiel LSP pour serveur langaguage protocol, il s'agit d'une extension Vscode qui permet de se rapprocher de l'expérience que l'on peut avoir dans `PhpStorm`, à un dépôt github dévélopper par symfony 
- thepiratebay.org



mettre à jour Symfony puis formulaire multi-step



**ngrok** : !!
- scoop install ngrok
- ngrok config add-authtoken $YOUR_AUTHTOKEN
- ngrok http 80 --url https://proud-gauntlet-elongated.ngrok-free.dev







**Symfony Reprise** : Le nouveau standart frontend pour les assets modernes via `Vite` et `RsBuild`
- L'avantage est que par défaut :
    - Temps de compilation instantané (HMR)
    - Gestion natives Sass, Typescript, Vue/React
    - Minimification et Code Splitting intégrés (ne renvoi pas tout le javascript de l'application au navigateur en une seule fois mais découpé l'app en plusieurs fichiers et charger uniquement ce qui est néccéssaire)
Une sorte d'adapteur 
Vite/RsBuild(génère les assets compilés) -> Fichiers JSON(création de entrypoints.json et manifest.json) -> RepriseBundle(lit et analyse les fichiers en temps réel) -> Templates Twig(Rendu final exact pour le navigateur)


**Stripe** : !! de `php`
- On.. le package `stripe/stripe-php` et dans le `.env` on.. `STRIPE_SECRET_KEY`
- On.. `StripeService`



**Pwa - Symfony**
- D'abord on transforme l'application en progressive webapp

web-push-php pour la notification push sur symfony :: pour configurer le serveur de not push, et on vas s'abonner à un pushmanager

- Librairy :
    - Openspout : permet de générer, lire des fichiers `xlsx`, `csv`, `ods` très rapide avec l'utilisation de la mémoire de moins de 3M : utile pour lire ou écrire de gros fichiers :: composer require openspout/openspout
        - dans `Domain/Exporter`, `ExcelFormatEnum`, `ExcelController`
        - Pour l'import de données `ExcelReaderFactory`, `Importer/`

    - dompdf : !! des fichiers `pdf`
        - On.. le bundle NucleosDompdfBundle `composer require nucleos/dompdf-bundle` qui est wrapper pour utiliser dompdf dans Symfony
            - Config `config/packages/nucleos_dompdf.yaml`
                - `defaults` - `chroot` : le chemin des assets du genre les images qu'on veut placer dans le fichier pdf
                    defaultFont: 'helvetica'
            - Dans `HomeController`, on a `PdfGeneratorService` pour gérer la génération de pdf
            - Une autre manière de rendre un fichier pdf est de le streamé, permet de télécharger le fichier directement
            - Pour insérer une image dans notre pdf `defaults` - `chroot` puis dans le `pdf.html.twig` mais attention il faut aussi redimensionné les images avant les mettre dans le pdf sinon ca va prendre un temps fou
            - dompdf 3 supporte des choses du css moderne, voir doc : pour la personnalisation avec le css on..





The content cannot be set on a StreamedResponse instance


C'est la même famille d'erreur que la première fois. `BinaryFileResponse`, comme `StreamedResponse`, interdit `setContent()`, car son contenu est le fichier lui-même. Quelque chose dans votre application essaie donc de modifier le contenu de la réponse **après** que le contrôleur l'a retournée. Votre contrôleur n'est probablement pas en cause : le coupable est en général un *listener* sur l'événement `kernel.response`.

## Trouver le coupable

La trace complète de l'exception (page d'erreur Symfony, onglet « Stack Trace », ou `var/log/dev.log`) vous indiquera quelle classe appelle `setContent()`. Les suspects habituels sont :

- un *event subscriber* de votre application ou d'un bundle qui réécrit le HTML des réponses (minification, injection d'un nonce CSP, remplacement de texte, etc.) sans vérifier le type de réponse ;
- un bundle tiers qui fait la même chose ;
- un listener qui intercepte une exception survenue pendant l'export et tente de remplacer le contenu.

La commande suivante liste tous les listeners branchés sur cet événement :

```bash
php bin/console debug:event-dispatcher kernel.response
```

Si le coupable est un listener à vous, il suffit de l'écarter pour ce type de réponse :

```php
public function onKernelResponse(ResponseEvent $event): void
{
    $response = $event->getResponse();

    if ($response instanceof BinaryFileResponse || $response instanceof StreamedResponse) {
        return;
    }

    // ... traitement existant
}
```

## Contournement immédiat

Si le listener vient d'un bundle que vous ne pouvez pas modifier, vous pouvez renvoyer une `Response` classique. Elle accepte `setContent()`, donc le listener ne plantera plus. Pour de petits exports comme le vôtre, c'est sans inconvénient :

```php
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;

$file = $this->excelExporter->export($columnNames, $data, $format);

$response = new Response(file_get_contents($file->getPathname()));
$response->headers->set('Content-Type', $format->contentType());
$response->headers->set(
    'Content-Disposition',
    HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, 'export.' . $format->extension())
);

unlink($file->getPathname());

return $response;
```

Le fichier est chargé entièrement en mémoire. Pour des exports de plusieurs dizaines de Mo, il vaut mieux corriger le listener et revenir à `BinaryFileResponse`.

Pouvez-vous coller la trace de l'exception, ou au moins les premières lignes qui suivent le message ? On verra tout de suite quelle classe appelle `setContent()`.


Le `dd()` montre que votre contrôleur renvoie toujours une `StreamedResponse`, c'est-à-dire le code de départ, et pas la version avec `$this->file()`. Deux choses expliquent probablement que « rien ne se passe ».






Le coupable est presque certainement **`App\EventListener\DogListener::onResponseEvent()`**, en première position de la liste. C'est le seul listener de votre application ; tous les autres viennent de Symfony ou de bundles connus, qui gèrent correctement les `BinaryFileResponse` et `StreamedResponse`. Votre `DogListener` doit appeler `$response->setContent(...)` sur toutes les réponses sans vérifier leur type.

## Correction

Ajoutez une garde au début de la méthode pour laisser passer les réponses qui n'ont pas de contenu modifiable :

```php
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

public function onResponseEvent(ResponseEvent $event): void
{
    $response = $event->getResponse();

    if ($response instanceof BinaryFileResponse || $response instanceof StreamedResponse) {
        return;
    }

    // ... votre traitement existant
}
```

Une variante plus générale consiste à vérifier `getContent()`. Ces deux types de réponse renvoient `false` au lieu d'une chaîne, donc ce test couvre aussi d'éventuelles autres sous-classes du même genre :

```php
$content = $response->getContent();
if ($content === false) {
    return;
}
```

Si `DogListener` ne doit agir que sur des pages HTML, vous pouvez aussi vérifier le `Content-Type` :

```php
if (!str_contains((string) $response->headers->get('Content-Type'), 'text/html')) {
    return;
}
```

Avec cette correction, vous pouvez revenir à `$this->file(...)` et `BinaryFileResponse`, et oublier le contournement avec `file_get_contents`.

## À propos du log

Les lignes que vous avez collées ne contiennent pas l'exception. Ce sont uniquement des *deprecations* de niveau `INFO`, dues au fait que vous êtes sur PHP 8.5 et que certaines dépendances ne sont pas encore à jour. Elles sont sans danger, mais elles noient tout le reste. Pour retrouver une vraie erreur, cherchez plutôt les lignes `CRITICAL` ou `ERROR` :

```powershell
Select-String -Path var\log\dev.log -Pattern "CRITICAL|\.ERROR" | Select-Object -Last 5
```

Si l'erreur persiste après la correction de `DogListener`, collez-moi ce que renvoie cette commande ou le contenu du listener.





## 2. Turbo intercepte peut-être le formulaire

Votre projet utilise plusieurs paquets Symfony UX (Typed, Mercure). Si **Symfony UX Turbo** est installé, c'est lui qui soumet le formulaire via `fetch()`. Or Turbo attend une redirection ou du HTML. Quand il reçoit un fichier binaire, il ne déclenche pas de téléchargement, et il ne se passe rien de visible.

Désactivez Turbo pour ce formulaire dans `export.html.twig` :

```twig
{{ form_start(form, {attr: {'data-turbo': 'false'}}) }}
    {{ form_widget(form) }}
    <button type="submit">Exporter</button>
{{ form_end(form) }}
```

Avec ces deux corrections, le `dd($response)` devrait afficher une `BinaryFileResponse` avec un en-tête `content-disposition: attachment; filename=export.xlsx`, et le navigateur devrait lancer le téléchargement.









- L'outil orval.dev permet à partir de fichiers openapi de générer les définitions typescript


**Docker** !!
- Le.. `compose.yaml` et `compose.override.yaml` sont le setup officiel généré par `Symfony`
    > Le fichier `compose.yaml` est pensé pour fonctionner partout, même en production *prod-friendly*
    > !! `compose.override.yaml` pour le dev uniquement et docker Compose charge **automatiquement** ce fichier en local
- docker compose up -d --build


docker-compose up --build

https://symfony.com/doc/current/setup/docker.html

docker pull dunglas/mercure

- `r` 





Pour un projet Symfony, il existe deux approches courantes. Depuis quelques années, l'équipe de FrankenPHP (avec le soutien de la communauté Symfony) maintient un modèle Docker officiel très complet, qui fournit automatiquement HTTPS, HTTP/2, HTTP/3 et le mode worker sans configuration supplémentaire, ce qui en fait le point de départ recommandé pour un nouveau projet. Je te montre cette approche en premier, puis l'approche classique PHP-FPM + Nginx pour comparaison.

## Approche recommandée : Symfony Docker (FrankenPHP)

**Démarrage rapide** — sur un nouveau projet, la CLI Symfony peut générer directement toute la configuration Docker :

```bash
symfony new monprojet --docker
cd monprojet
docker compose build --no-cache
docker compose up -d
```

Sur un projet existant, on peut ajouter la même configuration en récupérant le template `dunglas/symfony-docker` sur GitHub et en le fusionnant avec son projet.

**Ce que contient ce setup** — un `Dockerfile` construit sur l'image `dunglas/frankenphp`, qui embarque à la fois le serveur web et le runtime PHP dans un seul processus (plus besoin de conteneur Nginx séparé) :

```dockerfile
FROM dunglas/frankenphp

RUN install-php-extensions \
    pdo_pgsql \
    intl \
    opcache \
    zip

COPY --link . /app
WORKDIR /app

RUN composer install --no-dev --optimize-autoloader

ENV SERVER_NAME=:80
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
```

Un `Caddyfile` minimal à la racine du projet configure le routage vers `public/index.php` :

```
{$SERVER_NAME}

root * public/
php_server
```

Et un `compose.yaml` qui ajoute la base de données :

```yaml
services:
  php:
    build: .
    ports:
      - "80:80"
      - "443:443"
    environment:
      - DATABASE_URL=postgresql://app:app@database:5432/app?serverVersion=16
    volumes:
      - ./:/app
    depends_on:
      - database

  database:
    image: postgres:16-alpine
    environment:
      - POSTGRES_DB=app
      - POSTGRES_USER=app
      - POSTGRES_PASSWORD=app
    volumes:
      - db_data:/var/lib/postgresql/data

volumes:
  db_data:
```

Une fois lancé avec `docker compose up -d`, l'application est accessible directement en HTTPS sur `https://localhost` (certificat auto-signé généré automatiquement).










## Approche classique : PHP-FPM + Nginx

Avec un `nginx.conf` qui pointe vers `public/index.php` et transmet les requêtes PHP au conteneur `php` via `fastcgi_pass php:9000;`.

## Travailler au quotidien

Dans les deux cas, une fois les conteneurs démarrés :

```bash
# Commandes Symfony
docker compose exec php bin/console cache:clear
docker compose exec php bin/console make:controller
docker compose exec php bin/console doctrine:migrations:migrate

# Composer
docker compose exec php composer require symfony/mailer

# Voir les logs
docker compose logs -f php
```

**Un point important** : Symfony Flex ajoute souvent lui-même des services au `compose.yaml` quand tu installes certains paquets. Par exemple, `composer require doctrine/doctrine-bundle` déclenche une "recipe" qui propose d'ajouter automatiquement le service de base de données correspondant — pas besoin de l'écrire à la main dans la plupart des cas.

Si ton projet utilise Webpack Encore ou AssetMapper avec des dépendances Node, il est courant d'ajouter un service `node` séparé (ou une étape de build multi-stage) uniquement pour compiler les assets, sans l'embarquer dans l'image de production finale.












- Pour créer une base de données `Sqlite`, `symfony console d:d:c`

**2FA** : La double authentification via le bundle `SchebTwoFactorBundle`
- composer require 2fa : Le flex l'intercepte et installe les dépendances néccéssaire ou `composer require scheb/2fa-bundle`
    - Vous pouvez installer, si vous le souhaitez, des paquets supplémentaires pour étendre les fonctionnalités du bundle en fonction de vos besoins, par ex (Si on veut faire la double auth par email, on peut installer `composer require scheb/2fa-email` qui est une sorte d'extension au bundle) :
        composer require scheb/2fa-backup-code : Ajouter une fonctionnalité de codes de secours
        composer require scheb/2fa-trusted-device : Ajouter la fonctionnalité « Appareils de confiance »
        composer require scheb/2fa-totp : Ajouter une authentification à deux facteurs via TOTP
        composer require scheb/2fa-google-authenticator : Ajoutez l’authentification à deux facteurs avec Google Authenticator
        composer require scheb/2fa-email : Ajoutez une authentification à deux facteurs par e-mail
- La configuration `config/packages/scheb_2fa.yaml`
- Dans `config/routes/scheb_2fa.yaml`, vous devez ajouter deux routes :
un itinéraire pour le formulaire d'authentification à deux facteurs
une autre méthode pour vérifier le code d'authentification à deux facteurs 
- Pour le mettre en place `config/packages/security.yaml` - `firewalls` - `main`, puis `access_control` voir doc
- Ensuite dans `config/packages/scheb_2fa.yaml`, on vas préciser la méthode que scheb doit  `PostAuthenticationToken` et on peut commenté l'autre
    Étape 5 : Configurer les jetons de sécurité
    Votre pare-feu peut proposer différentes méthodes de connexion. Par défaut (sans configuration), le module n'écoute que les jetons suivants :
        Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken(authentification par nom d'utilisateur et mot de passe)
        Symfony\Component\Security\Http\Authenticator\Token\PostAuthenticationToken(jeton par défaut utilisé par les authentificateurs)
        Si vous souhaitez prendre en charge l'authentification à deux facteurs avec une autre méthode de connexion, vous devez enregistrer sa classe de jeton dans les scheb_two_factor.security_tokensoptions de configuration.
- Étape 6 : Activer les méthodes d’authentification à deux facteurs
    Si vous avez installé l'une des méthodes d'authentification à deux facteurs fournies en tant que sous-package, vous devez l'activer séparément. Consultez la procédure pour :
    - scheb/2fa-totp Authentification TOTP
    scheb/2fa-google-authenticator Authentificateur Google
    scheb/2fa-email Authentification par code via e-mail

- On a choisi l'Authentification par code via e-mail (Un fournisseur d'authentification à deux facteurs pour générer un code numérique aléatoire et l'envoyer à l'utilisateur par e-mail.) 

Comment fonctionne l'authentification ?
Une fois l'authentification réussie, un numéro aléatoire est généré et enregistré dans le compte utilisateur. Ce numéro est ensuite envoyé à l'utilisateur par courriel. L'utilisateur doit alors le saisir pour accéder au compte.

Le nombre de chiffres peut être configuré et l'activation ect.. : dans `config/packages/scheb_2fa.yaml`
- Puis on modifie le `User` vu qu'on devra sauvegarder le code qui va permettre de faire la double-authentification via l'interface `TwoFactorInterface`
- Si il est activer par défaut `email - enabled: true`, quand on vas se connceter le bundle va comprendre qu'il y'a eu une connexion et vas nous envoyer un mail d'auth (utiliser docker pour que le serveur de développement de Symfony détecte qu'il y'a des variables d'env qui lui sont arrivées from docker et notemment un serveur de webmail)
- On peut personnalisé le Modèle de formulaire d'authentification, voir doc
- Pour que l'utilisateur puisse indiquer que l'appareil sur lequel il est connécté est fiable pour éviter de toujours lui demander de s'authentifié à deux fois, pour ça on vas mettre en place les appareils de confiance via le bundle `composer require scheb/2fa-trusted-device`, puis on l'a configurer dans `config/packages/scheb_2fa.yaml` - `trusted_device`, puis dans `config/packages/security.yaml` - `firewalls` - `main` - `two_factor`.. on indique qu'il faut l'utiliser


- Ne pas oublié de gérer le cas ou il doit être activer par l'utilisateur via le profil !!

- Via totp : La méthode la plus courante est le bundle **scheb/2fa-bundle**, avec des codes **TOTP** à 6 chiffres générés par une application comme Google Authenticator, Microsoft Authenticator ou Authy
```bash
composer require scheb/2fa-bundle scheb/2fa-totp
composer require endroid/qr-code   # pour afficher le QR code
```

```yaml
scheb_two_factor:
    ...
    totp:
        enabled: true
        issuer: 'Mon Site'          # nom affiché dans l'application
        template: security/2fa_form.html.twig
```
Voir doc


## Bonnes pratiques à ajouter

- **Codes de secours** (`scheb/2fa-backup-code`) : des codes à usage unique, utiles si l'utilisateur perd son téléphone.
- **Appareils de confiance** (`scheb/2fa-trusted-device`) : une case « Ne plus demander sur cet appareil pendant 30 jours ».
- **Limiter les tentatives** : le bundle propose une option pour bloquer les essais répétés (force brute).
- **Chiffrer le secret en base** : s'il fuit, la 2FA ne protège plus rien.
- **Code par email** (`scheb/2fa-email`) : une alternative pour les utilisateurs sans application, mais moins sûre.
- **HTTPS obligatoire** en production.







**8.2**
- Les `Dto` peuvent joué le rôle de formulaire via l'attribut `#[AsFormType]` et sur ses propriétés `#[FormField(EmailType::class)]`, `#[FormField]`.. dans le controller `createForm(Dto::class)`
- Les workflows (le cycle de vie dans la class) via des attributs
- Un bundle par composant au lieu du `FrameworkBundle` au par avant (Mailer, Cache, Form..), maintenant on aura plus
- Chiffrement des données avec doctrine via l'attribut `#[Encrypted(key: 'customer.contact')]` sur nos propriétés (ex: L'application stocke des données sensibles comme des coordonnées personnels et on veut que les informations soit chiffrés dans la bd et aussi pouvoir à les maninipulés)
- L'authentification par OpenID Connect sans bundle tiers : permet de se connecter avec un compte microsoft ou google ou keycloak
- Reconfirmé l'identité, pas la session via le `#[IsGranted('IS_AUTHENTICATED_VERY_RECENTLY')]` pour les actions sensibles : Action sensible -> Réauth -> Action autorisée(mais la session ne change pas), il y'a deux durées de références 2H et 5min et peut être configuré, l'intérêt est de limité les risques lorsqu'une session est resté ouverte pendant longtemps sur un ordinateur auquel quelqu'un pourrait accéder :: Appelé le SUDO MODE
- Messenger 
    - La base et le broker, même transaction : pour le message async messenger (le message ne part que si la commande est enregistrée) `outbox`
    - Ne jamais rejouer un message `inbox`
    - Un worker, plusieurs messages en parallèle via le paramètre  `php.. messenger:consume async --concurrency=4`
- Email templates
Plus d'infos : `symfony.com/doc/current/` doc officielle, `../blog/category/living-on-the-edge/8.2` tous les articles






- 
Php - Langage Backend
  - Présentation du langage
  - Installation du langage
  - Les variables
  - !! tableaux
  - !! conditions
  - !! boucles
  - !! fonctions
  - !! fonctions utilisateurs
  - Require & include
  - Php & html
  - Traitement des formulaires
  - Les dates
  - Lecture de fichiers
  - Ecriture de fichiers
  - Les cookies
  - La session
  - Tp.. Compteur de vues
  - !! dashbord
  - !! système de connexion
  - Chiffrer les mots de passe
  - L'objet DateTime
  - Les class
  - !! Statique
  - L'héritage des class
  - Tp.. Livre d'or
  - Utiliser une API avec cURL
  - Les Exceptions
  - PHPDoc
  - PDO - Connexion à une base de données
  - Les espaces de noms `namespace`
  - L'autoloader
  - Utiliser des librairies tierces
  - Les fonctions anonymes
  - Le router
  - ob_start
  - Tp.. Tableau dynamique
  - !! Classe d'authentification
  - !! Tester son code
  - !! Pratiquer les tests
  - !! QueryBuilder
  - !! Classe Table
  - Présentation du projet
    - Création de la structure
    - Le routeur
    - Remplir la base de données
    - Listing d'articles
    - Pagination
    - Simplifions l'URL
    - Page article
    - Page catégorie
    - Réorganisation
    - Affichage des catégories sur le listing
    - Création de la classe Table
    - Administration du site
    - Edition d'un article
    - Gestion des formulaires
    - Valider les données
    - Création d'un article
    - Gestion des catégories
    - Liaison article catégorie
    - Upload d'images !!
    - Authentification
    - Mise en ligne

Symfony - Framework backend
  - Présentation du framework
  - Installation du framework
  - Nos premières pages
  - Moteur de templates
  - L'ORM Doctrine
  - Les formulaires
  - Valider les données
  - Comprendre les services
  - Asset et Mapper - Pour gérer les assets
  - TP Formulaire de contact
    - Envoi d'email via le composant Mailer
  - TP Catégorie
  - ORM relation ManyToOne
  - Envoi de fichiers
  - Le composant sécurité
  - Paginer les données
  - Doctrine avancé Select partiel et DQL
  - Création d'une API, le serializer
  - Création d'une API, Désérialisation et MapRequestPayload
  - Créer une API, Authenticator stateless
  - Les fixtures pour remplir la base de données
  - Permissions avancées avec les Voter
  - Les évènements
  - Symfony UX
  - Formulaires imbriqués
  - Tâche asynchrone avec Messenger
  - Internationalisation
  - Héberger sur un mutualisé O2Switch via ssh et git
  - Héberger sur un mutualisé Infomaniak via ssh et git
  - ValueResolver

- API Platform - Framework API REST
  - Présentation du framework
  - Installation du framework
  - La sérialisation
  - La validation
  - Pagination et Filtres
  - Fonctionnement d'API Platform
  - Opération personnalisé
  - Améliorer la documentation OpenAPI
  - Créer un DataProvider
  - Créer un DataPersister
  - PUT vs PATCH
  - Authentification JSON
  - !! Form HTML
  - !! JWT
  - Rafraichir les tokens JWT
  - !! par clef d'API
  - Limiter les ressources par utilisateur via DoctrineExtension
  - Injecter l'utilisateur via un Denormalizer
  - Limiter les champs exposés en changeant les groupes de serialization dynamiquement
  - Envoi de fichiers
  - Désérializer du multipart

JavaScript - Langage intéractif :: Le principale
Css - Langage de style :: Le principale
SQL - Langage pour communiquer avec une base de données
React - Framework frontend


Question pour dev :
- Le dernier ticket (problème de concurence) : Tu développe une appliction pour une platform de billeterie, il reste un seule ticket, 2 personnes clique sur acheter au même moment depuis 2 pays différents, les 2 transactions arrive presque simultanement sur deux serveurs distints
  - Question : Comment garentir qu'un seul utilisateur obtienne le ticket même si les 2 paiements sont lancés en parallèle et que faire si le paiement du gagnant échoue
- Le paiement fantôme (problème d'idempotence) : Un client éffectue un paiement de 500$, le serveur traite correctement la transaction mais la connexion internet coupe avant que le client reçoive la confirmation, le client clique de nouveau sur payer entre-temps, son application a également déclenché trois tentatives automatiques
  - !! : Comment garentir que le client ne sera débité qu'une seule fois même si le serveur reçoit 5 requêtes identiques ou si plusieurs workers les traitent simultanément
- La base de données qui tombe (problème de résilience) : Tu développe une application bancaire utilisé par 2milions de personnes. Pendant un transfert d'argent le serveur débite le compte A de 100$ puis tombe avant de créditer le compte B
  - !! : Comment concevoir ton système pour qu'aucun argent ne disparaisse et qu'aucun transfert ne soit exécuter 2 fois même si un serveur tombe au milieu de l'opération
- Un milion d'utilisateur (problème de scalabilité) : Ton application fonctionne parfaitement avec 1.000 utilisateurs, un influenceur partage son lien et en quelque minutes 1.000.000 de personnes tentent de se connecter, le CPU atteint 100%, la base de données sature et le site devient inaccessible
  - !! : Comment concevoir une architecture capable d'absorber ce trafic sans perdre de données sans mulitiplier inutilement les coûts et sans rendre l'application indisponible
- Le compte administrateur (problème de sécurité) : Tu développe une application ou chaque utilisateur possède un identifiant, un utilisateur authentifié découvre qu'en remplaçant /api/user/5 en /api/user/6 il peut consulter les informations privées d'un autre utilisateur
  - !! : Comment empêcher définitivement ce type d'accès non autorisé même si l'attaquant manipule les requêtes HTTP, les identifiants et les paramètres envoyés au serveur
- Le serveur qui tombe pendant une commande (problème de fiabilité) : Un client commande un produit, le paiement est validé, le stock est dimunué mais le serveur tombe avant d'enregister la commande, le client à payer mais aucune commande n'apparaît dans son historique
  - !! : Comment garentir la cohérence entre le paiement, le stock et la commande lorsque ces opérations sont exécutées par trois services indépendants
- La mise à jour sans intéruption (problème de déploiment) : Ton application est utilisé 24h/24h, tu dois modifier un colonne critique de la base de données et déployer une nouvelle version du backend, mais des miliers d'utilisateur utilisent encore l'ancienne version pendant le déploiment
  - !! : Comment effectuer cette migration sans intérompre le service, sans cassé l'ancienne version et avec la possibilité de revenir en arrière si le nouveau code présente un bug
- Deux appareils, une même donnée (problème de syncronisation) : Un utilisateur ouvre son application de gestion financière sur deux téléphones, sur le premier, sans connexion internet, il modifie son buget de 500$ à 300$, sur le sécond également hors-ligne il le modifie de 500$ à 700$, les deux téléphones retrouvent internet au même moement 
  - !! : Quel valeur le système doit-il conserver, comment détecter et résoudre ce conflit sans écraser silencieusement une modification légitime
- L'intéligence artificielle qui invente (problème de fiabilité de l'IA) : Tu intègre une IA dans une application juridique, un utilisateur pose une question sur le droit du travail, l'IA répond avec assurence en citant un article de loi inexistent, la répense semble parfaitement crédible
  - !! : Comment concevoir une architecture qui vérifie les références juridiques, distingue les informatons vérifiées des interprétations et refuse de repondre lorsque les preuves sont insuffisantes
- 2 serveurs, 2 vérités (problème de système distribués) : Tu développe une platforme banquaire avec des serveurs en Afrique et en Europe, une panne réseau empêche temporairement les deux régions de communiquer. Pendant cette panne un utilisateur tente de retirer les même 1000$ depuis deux régions différentes, chaque serveur possède une copie  des données indiquant un solde de 1000$ 
  - !! : Comment empêcher les deux retraits d'être validés ? faut-il bloquer certaines opérations pendant la panne ou privilégier la disponibilité ? Comment justifier ce choix

:: Donne moi d'autres question de ce genre




















Vu tout ce que nous avons construit ensemble, si ton objectif est d'avoir un **logiciel professionnel de gestion de compagnies de transport** (multi-entreprises, multi-gares, réservations, exploitation, temps réel, application mobile, etc.), je partirais sur une architecture moderne plutôt que de simplement changer de framework.

## Backend

Je garderais **Symfony**.

Pourquoi ?

* Tu maîtrises déjà Symfony.
* API Platform est excellent pour construire des API REST.
* L'écosystème est mature.
* Facile à faire évoluer vers des microservices plus tard si nécessaire.

J'ajouterais :

* **API Platform**
* **PostgreSQL** (je le préfère à MySQL pour ce type d'application)
* **Redis** (cache, files d'attente, sessions, verrous)
* **Messenger** (traitements asynchrones)
* **Mercure ou WebSocket** pour le temps réel

---

## Frontend Web

Je passerais sur **Next.js**.

Pourquoi ?

* Très performant.
* Excellent avec React.
* Facile pour construire un tableau de bord riche.
* Très bon pour les SaaS.

Tu avais d'ailleurs demandé si React ou Next.js était préférable pour consommer ton backend Symfony : je resterais sur **Next.js**.

---

## Mobile

Je garderais **Flutter**.

Tu as déjà créé le projet.

Un seul code pour :

* Android
* iOS

---

## Temps réel

Pour le suivi des voyages, je ne me limiterais pas à Mercure.

Je mettrais :

* WebSocket
* ou Mercure si tu restes sur Symfony

Pour afficher :

* position du car
* retards
* ventes
* nouveaux tickets
* nouvelles réservations

sans recharger la page.

---

## Cartographie

Pour le suivi GPS :

* Google Maps
* ou OpenStreetMap + Leaflet (moins coûteux)

Tu pourras afficher :

* le trajet ;
* la position du car ;
* les gares ;
* les retards.

---

## Notifications

* Firebase Cloud Messaging (Flutter)
* Emails
* SMS (si besoin)

---

## Authentification

* JWT
* Refresh Token
* Gestion fine des rôles (tu as déjà un bon modèle RBAC)

---

## Déploiement

* Docker
* Nginx
* GitHub Actions
* VPS ou Cloud

---

## Base de données

Je passerais sur **PostgreSQL**.

Avec notamment :

* index performants ;
* transactions solides ;
* types avancés ;
* possibilité d'utiliser des extensions géographiques si tu ajoutes beaucoup de fonctionnalités GPS.

---

# Si tu veux vraiment du temps réel

Je pense que ton application aura besoin de deux types de temps réel.

### 1. Temps réel métier

Exemple :

* un ticket est vendu ;
* une réservation est créée ;
* un voyage est annulé.

Tous les écrans concernés se mettent à jour automatiquement.

---

### 2. Temps réel GPS

Le chauffeur possède l'application Flutter.

Toutes les 10 à 30 secondes :

```text
Latitude

Longitude

Vitesse

Heure
```

sont envoyées au serveur.

L'application web affiche alors :

* le car sur la carte ;
* le retard ;
* le prochain arrêt.

---

# Si je devais repartir de zéro aujourd'hui

Je choisirais :

| Couche          | Technologie                             |
| --------------- | --------------------------------------- |
| Backend         | Symfony + API Platform                  |
| Base de données | PostgreSQL                              |
| Cache           | Redis                                   |
| Temps réel      | Mercure + WebSocket (selon les besoins) |
| Web             | Next.js + React Query + TypeScript      |
| Mobile          | Flutter                                 |
| Cartographie    | Google Maps ou Leaflet                  |
| Auth            | JWT + Refresh Token                     |
| Déploiement     | Docker + Nginx + GitHub Actions         |

## Mon dernier conseil

Au vu de l'évolution de ton projet, je pense que le plus grand changement ne sera **pas la technologie**, mais **l'architecture métier**.

Tu commences à intégrer des notions d'exploitation en temps réel (passage dans les gares, retards, suivi des cars, bordereaux, statistiques, réservations, multi-gares). À ce stade, je structurerais le projet autour de grands domaines fonctionnels (Administration, Exploitation, Billetterie, Réservations, Flotte, Stock, Comptabilité, etc.) plutôt que d'une simple organisation par entités.

C'est cette organisation qui fera réellement passer ton application d'un CRUD évolué à un logiciel professionnel de gestion de compagnies de transport.







Si je fais abstraction de tes préférences et que je me place dans le contexte d'un **éditeur de logiciel (SaaS) pour compagnies de transport** qui vise plusieurs centaines de compagnies et des milliers de voyageurs, voici la stack que je choisirais aujourd'hui.

## Backend

* **Java 21**
* **Spring Boot 3**
* Spring Security
* Spring Data JPA
* Spring WebFlux (si besoin de traitements réactifs)
* Hibernate

Pourquoi ?

* C'est le standard des grandes entreprises.
* Excellentes performances.
* Très robuste.
* Énorme écosystème.
* Très adapté aux applications critiques.

---

## Base de données

* **PostgreSQL**

Pourquoi ?

* Très fiable.
* Transactions ACID.
* Excellent pour les fortes charges.
* Support GIS avec PostGIS.
* Très utilisé dans le transport.

---

## Cache

* Redis

Pour :

* sessions
* cache
* disponibilité des sièges
* notifications

---

## Messaging

* Apache Kafka

Pour gérer :

* Ticket vendu
* Réservation créée
* Paiement reçu
* Position GPS
* Notification

Chaque événement est diffusé aux autres services.

---

## Temps réel

* WebSocket
* Kafka

Le GPS des cars est envoyé en temps réel.

Les tableaux de bord se mettent à jour automatiquement.

---

## GPS

* PostGIS
* OpenStreetMap
* Google Maps (si le budget le permet)

---

## Frontend Web

* React
* Next.js
* TypeScript

Aujourd'hui, c'est probablement la combinaison la plus utilisée.

---

## Mobile

* Flutter

Pourquoi ?

* Android
* iOS
* Web (si besoin)

Une seule base de code.

---

## Authentification

* OAuth2
* OpenID Connect
* JWT

---

## Recherche

* Elasticsearch

Pour rechercher rapidement :

* voyageurs
* tickets
* voyages
* bagages

---

## Stockage

* S3 (ou compatible S3 comme MinIO)

Pour :

* tickets PDF
* pièces jointes
* images

---

## Monitoring

* Prometheus
* Grafana

Pour surveiller :

* CPU
* mémoire
* temps de réponse
* nombre de voyages
* erreurs

---

## Logs

* Loki
* Grafana

ou

* ELK Stack

---

## Conteneurisation

* Docker

---

## Orchestration

* Kubernetes

---

## CI/CD

* GitHub Actions

ou

* GitLab CI

---

# Si l'application devient très grosse

Je passerais progressivement en microservices.

```text
API Gateway

├── Auth Service
├── Billetterie Service
├── Réservation Service
├── Exploitation Service
├── GPS Tracking Service
├── Paiement Service
├── Notification Service
├── Stock Service
├── Reporting Service
```

Chaque service possède sa propre base de données.

Ils communiquent via Kafka.

---

# Pourquoi Java plutôt que Node, Go ou .NET ?

Pour un logiciel de transport, les critères sont :

* disponibilité 24h/24 ;
* transactions fiables ;
* concurrence importante ;
* maintenance sur plusieurs années.

Dans ce contexte :

* **Java/Spring Boot** est un choix très répandu chez les grands éditeurs de logiciels métiers.
* **.NET** serait également un excellent choix, notamment dans les entreprises fortement orientées Microsoft.
* **Go** est remarquable pour les services très performants (par exemple un service de suivi GPS), mais son écosystème métier est moins riche.
* **Node.js** est très performant pour les applications temps réel, mais beaucoup d'éditeurs privilégient Java ou .NET pour les traitements métiers complexes.

---

# La stack que j'utiliserais pour un SaaS de niveau entreprise

| Couche           | Technologie                  |
| ---------------- | ---------------------------- |
| Backend          | Java 21 + Spring Boot        |
| Base de données  | PostgreSQL + PostGIS         |
| Cache            | Redis                        |
| Temps réel       | WebSocket                    |
| Bus d'événements | Apache Kafka                 |
| Frontend         | Next.js + React + TypeScript |
| Mobile           | Flutter                      |
| Cartographie     | OpenStreetMap ou Google Maps |
| Recherche        | Elasticsearch                |
| Stockage         | S3 / MinIO                   |
| Monitoring       | Prometheus + Grafana         |
| Logs             | Loki ou ELK                  |
| Conteneurisation | Docker                       |
| Orchestration    | Kubernetes                   |
| CI/CD            | GitHub Actions ou GitLab CI  |

C'est une architecture que l'on retrouve, avec des variantes, dans de nombreuses plateformes professionnelles de logistique, de transport et de mobilité. Elle est conçue pour être scalable, résiliente et maintenable sur le long terme.









Le **temps réel** est une notion qui est souvent mal comprise. Beaucoup pensent que cela signifie "rafraîchir la page toutes les secondes", alors qu'en réalité, le principe est différent.

---

# Le principe du temps réel

Dans une application classique, c'est le **client** qui demande les informations.

```text
Navigateur
      │
      │ "Y a-t-il du nouveau ?"
      ▼
Serveur
      │
      ▼
Réponse
```

Par exemple :

* tu ouvres la liste des tickets ;
* tu appuies sur F5 ;
* le navigateur redemande les données.

C'est le modèle **Request / Response**.

---

## En temps réel

C'est l'inverse.

Le navigateur ouvre une connexion qui reste active.

```text
Navigateur
      ▲
      │
Connexion ouverte
      │
      ▼
Serveur
```

Lorsque quelque chose se produit :

```text
Ticket vendu

↓

Serveur

↓

Le serveur pousse l'information

↓

Tous les navigateurs concernés sont mis à jour.
```

Le client n'a rien demandé.

---

# Exemple dans ton application

Imagine trois postes.

```text
Guichet Abidjan

Commercial

Administrateur
```

Le commercial vend un ticket.

Sans temps réel :

* le guichet ne voit rien ;
* l'administrateur ne voit rien.

Ils doivent actualiser.

---

Avec le temps réel :

```text
Commercial

↓

Ticket vendu

↓

Serveur

↓

Notification

↓

Guichet

↓

Administrateur
```

Tout se met à jour automatiquement.

---

# Le GPS

Même principe.

Le téléphone Flutter envoie :

```text
Latitude

Longitude

Vitesse
```

Toutes les 15 secondes.

Le serveur reçoit.

Puis :

```text
Serveur

↓

WebSocket

↓

Toutes les cartes affichent
la nouvelle position.
```

---

# Les technologies

Il existe plusieurs solutions.

## Polling

Toutes les 5 secondes :

```text
Client

↓

As-tu du nouveau ?

↓

Serveur

↓

Non
```

Puis encore :

```text
Client

↓

As-tu du nouveau ?

↓

Serveur

↓

Non
```

Puis :

```text
Client

↓

As-tu du nouveau ?

↓

Serveur

↓

Oui
```

Simple.

Mais très gourmand.

---

## Long Polling

Le serveur garde la requête ouverte.

Moins de trafic.

---

## Server Sent Events (SSE)

Le serveur envoie uniquement des messages.

Symfony utilise **Mercure**.

Très pratique.

---

## WebSocket ⭐⭐⭐⭐⭐

Le navigateur ouvre une connexion.

Elle reste ouverte.

Les deux côtés peuvent envoyer des données.

```text
Navigateur
⇅
Serveur
```

C'est la solution la plus complète.

---

# Est-ce que cela fonctionne sur un hébergement mutualisé ?

La réponse est : **ça dépend de la technologie utilisée.**

## Polling

✅ Oui.

Tous les hébergeurs le permettent.

Mais ce n'est pas du vrai temps réel.

---

## Mercure

En général :

❌ Non sur un mutualisé classique.

Pourquoi ?

Mercure nécessite un serveur dédié (ou un service managé) qui garde des connexions ouvertes en permanence. Les hébergements mutualisés autorisent rarement ce type de processus.

---

## WebSocket

Là encore :

❌ Généralement non.

Les WebSockets demandent un serveur qui reste actif en permanence, ce qui est incompatible avec la plupart des offres mutualisées.

---

# Pourquoi ?

Sur un mutualisé :

```text
Apache

↓

PHP démarre

↓

Traite la requête

↓

PHP s'arrête
```

Il n'y a aucun processus qui reste vivant.

---

En WebSocket :

```text
Serveur

↓

Reste vivant

24h/24

↓

Écoute les connexions
```

Il faut donc un VPS, un serveur dédié ou un service spécialisé.

---

# Dans ton cas

Tu m'avais indiqué que tu utilises actuellement un **hébergement mutualisé**.

Je procéderais ainsi :

### Phase 1

Application classique.

À chaque action importante :

* vente de ticket ;
* réservation ;
* bagage ;

on fait un rafraîchissement intelligent.

Par exemple avec **React Query** ou **TanStack Query** :

* toutes les 10 secondes ;
* ou au focus de la fenêtre.

Le coût reste faible.

---

### Phase 2

Quand ton SaaS commencera à avoir plusieurs compagnies clientes et un besoin de suivi en direct (GPS, tableau d'exploitation, notifications instantanées), tu migreras vers un **VPS** ou un cloud.

À ce moment-là, tu pourras activer :

* WebSockets ;
* Mercure ;
* Redis ;
* éventuellement Kafka si la plateforme prend beaucoup d'ampleur.

---

## Ma recommandation

Pour ton application actuelle, **je ne chercherais pas à implémenter un véritable temps réel tant que tu restes sur un hébergement mutualisé**. Le coût et les contraintes techniques ne sont pas justifiés.

En revanche, je **concevrais dès maintenant l'architecture** (événements métier, services, séparation des responsabilités) de manière à pouvoir remplacer plus tard un simple polling par du WebSocket ou Mercure sans devoir réécrire toute l'application. Ainsi, tu pourras évoluer vers un véritable temps réel lorsque l'infrastructure le permettra.
















Le **backup** (ou **sauvegarde**) est le principe qui consiste à **conserver une copie des données** afin de pouvoir les restaurer en cas de problème.

Le mot clé est **restauration**. Une sauvegarde n'a de valeur que si elle peut être restaurée.

---

# Pourquoi faire des backups ?

Imagine que ton application de transport est en production.

Un jour :

* un développeur supprime accidentellement la table `reservation` ;
* une mise à jour contient un bug qui efface des données ;
* le disque du serveur tombe en panne ;
* un pirate supprime la base de données ;
* une migration échoue et corrompt certaines données.

Sans sauvegarde, tu perds tout.

Avec un backup, tu peux revenir à un état précédent.

---

# Que sauvegarde-t-on ?

## 1. La base de données

C'est le plus important.

Par exemple :

* utilisateurs ;
* réservations ;
* billets ;
* paiements ;
* voyages.

Sous PostgreSQL :

```bash
pg_dump transport > backup.sql
```

Sous MySQL :

```bash
mysqldump transport > backup.sql
```

---

## 2. Les fichiers

Par exemple :

* les avatars ;
* les logos des entreprises ;
* les pièces jointes ;
* les factures PDF ;
* les billets PDF.

Si tu sauvegardes seulement la base mais pas les fichiers, tu risques d'avoir des références vers des fichiers inexistants après une restauration.

---

## 3. Les configurations

On sauvegarde également :

* les variables d'environnement (`.env`) ;
* les certificats SSL ;
* les configurations du serveur ;
* les scripts de déploiement.

---

# Les différents types de backups

## 1. Backup complet (Full Backup)

On copie tout.

Exemple :

```text
Base de données entière
+
Tous les fichiers
```

Avantages :

* restauration simple ;
* fiable.

Inconvénients :

* prend du temps ;
* consomme plus d'espace disque.

---

## 2. Backup incrémental

On sauvegarde uniquement ce qui a changé depuis la dernière sauvegarde.

Exemple :

Lundi :

```
100 Go
```

Mardi :

```
+ 300 Mo
```

Mercredi :

```
+ 150 Mo
```

Jeudi :

```
+ 700 Mo
```

On ne recopie jamais les 100 Go à chaque fois.

C'est beaucoup plus rapide.

---

## 3. Backup différentiel

On sauvegarde tout ce qui a changé depuis le dernier **backup complet**.

Exemple :

Lundi :

```
Full Backup
100 Go
```

Mardi :

```
500 Mo
```

Mercredi :

```
900 Mo
```

Jeudi :

```
1,2 Go
```

Chaque sauvegarde contient toutes les modifications depuis lundi.

---

# Fréquence des sauvegardes

Cela dépend de l'application.

Pour un SaaS de transport :

* sauvegarde complète chaque nuit ;
* sauvegarde incrémentale toutes les heures (ou plus fréquemment selon les besoins).

Ainsi, si un problème survient à 14h30, tu ne perds qu'une petite partie des données récentes.

---

# Où stocker les backups ?

Une erreur fréquente consiste à sauvegarder sur le même serveur que l'application.

```
Serveur

Application
Base
Backup
```

Si le disque tombe en panne, tout disparaît.

La bonne pratique est de stocker les sauvegardes sur un autre support :

```
Serveur A
Application

↓

Serveur B
Backups
```

ou dans un stockage cloud.

---

# La restauration (Restore)

Faire un backup ne suffit pas.

Il faut pouvoir restaurer.

Exemple :

```bash
psql transport < backup.sql
```

ou

```bash
mysql transport < backup.sql
```

De nombreuses entreprises testent régulièrement leurs sauvegardes sur un serveur de test pour vérifier qu'elles sont réellement exploitables.

---

# La règle du 3-2-1

C'est une bonne pratique très connue :

* **3 copies** des données ;
* sur **2 supports différents** ;
* dont **1 copie hors site** (autre serveur ou cloud).

Exemple :

* les données de production ;
* une sauvegarde sur un NAS ;
* une sauvegarde dans un stockage cloud.

Ainsi, même si un site entier est indisponible, tu conserves une copie.

---

# Backup ≠ Haute disponibilité

Ces notions sont souvent confondues.

Le **backup** sert à récupérer des données perdues.

La **haute disponibilité** sert à maintenir le service en ligne lorsqu'un serveur tombe en panne.

Par exemple :

* si un développeur supprime accidentellement 10 000 réservations, un serveur de secours répliquera aussi cette suppression. Il faudra restaurer les données depuis une sauvegarde.
* si un serveur tombe en panne mais que tu as un second serveur prêt à prendre le relais, les utilisateurs peuvent continuer à utiliser l'application sans interruption.

Ces deux mécanismes sont complémentaires.

---

# Exemple concret avec ton SaaS de transport

Imaginons qu'à **14h00**, une migration défectueuse supprime toutes les réservations.

Tu disposes de :

* une sauvegarde complète réalisée à **00h00** ;
* des sauvegardes incrémentales effectuées toutes les heures : **01h00, 02h00, ..., 13h00**.

Tu peux restaurer :

1. la sauvegarde complète de **00h00** ;
2. puis appliquer les sauvegardes incrémentales jusqu'à **13h00**.

Tu récupères ainsi presque toutes les données. Les seules informations potentiellement perdues sont celles créées entre **13h00** et **14h00**, sauf si tu disposes d'une fréquence de sauvegarde encore plus élevée ou d'autres mécanismes comme les journaux de transactions.

En résumé, les sauvegardes sont une composante essentielle de l'exploitation d'une application en production. Elles protègent contre les erreurs humaines, les bugs, les pannes matérielles et les incidents de sécurité, et elles doivent toujours être pensées avec leur stratégie de restauration, leur fréquence, leur emplacement de stockage et des tests réguliers de récupération.









Le **backfill** est un principe qui consiste à **remplir des données manquantes après coup**. En d'autres termes, on exécute un traitement qui met à jour des données déjà présentes dans la base pour les rendre compatibles avec une nouvelle fonctionnalité ou une nouvelle règle métier.

### Exemple simple

Imaginons que tu aies une table `voyage` :

| id | destination  | distance |
| -- | ------------ | -------- |
| 1  | Yamoussoukro | 240      |
| 2  | Bouaké       | 350      |

Quelques mois plus tard, tu ajoutes une nouvelle colonne :

```sql
temps_estime
```

Les nouveaux voyages auront cette valeur, mais les anciens auront `NULL`.

Un **backfill** consiste à parcourir tous les anciens voyages et calculer :

```text
temps_estime = distance / vitesse_moyenne
```

À la fin :

| id | destination  | distance | temps_estime |
| -- | ------------ | -------- | ------------ |
| 1  | Yamoussoukro | 240      | 3h30         |
| 2  | Bouaké       | 350      | 5h           |

---

## Pourquoi fait-on un backfill ?

Principalement pour :

* remplir une nouvelle colonne ;
* recalculer une donnée devenue obsolète ;
* corriger des données existantes ;
* migrer d'un ancien format vers un nouveau.

---

## Dans Symfony

On crée souvent une **commande Console** :

```bash
php bin/console app:backfill:temps-voyages
```

Cette commande :

1. récupère tous les voyages ;
2. calcule `temps_estime` ;
3. sauvegarde les modifications.

---

## Exemple dans ton projet de transport

Imaginons que tu ajoutes un champ :

```php
Reservation::canalVente
```

Avant, il n'existait pas.

Tu veux que :

* les réservations créées par l'application → `APPLICATION`
* les réservations faites au guichet → `GUICHET`

Toutes les anciennes réservations ont `NULL`.

Tu lances alors un backfill qui analyse les anciennes données et remplit automatiquement ce champ.

---

## Différence avec une migration Doctrine

Une **migration** modifie la structure de la base :

```sql
ALTER TABLE reservation
ADD canal_vente VARCHAR(20);
```

Le **backfill** modifie les **données** :

```text
Réservation 1 → APPLICATION
Réservation 2 → GUICHET
Réservation 3 → APPLICATION
...
```

On fait souvent les deux dans cet ordre :

1. Migration → ajout de la colonne.
2. Déploiement de la nouvelle version de l'application.
3. Backfill → remplissage des anciennes données.
4. (Éventuellement) rendre la colonne obligatoire (`NOT NULL`) une fois toutes les données complétées.

---

## Pourquoi est-ce important dans une application en production ?

Sur une application utilisée par des clients, il peut y avoir des millions d'enregistrements. Si tu rends directement une nouvelle colonne obligatoire sans backfill, l'application risque de planter sur les anciennes données. Le backfill permet de faire évoluer la base progressivement, sans interrompre le service.

Pour une application comme ton SaaS de transport, le backfill est particulièrement utile lorsque tu ajoutes de nouveaux champs (par exemple un nouveau statut, un indicateur métier ou un canal de vente) et que tu souhaites que toutes les anciennes données soient cohérentes avec les nouvelles règles métier.















Plus loin : la double authentification pour les administrateurs.

## 2. Messenger sur mutualisé

**Oui, ça marche** — le transport Doctrine est fait pour ça. Les messages vont dans une table, un cron consomme :

```bash
* * * * * cd /home/user/webpesee && php bin/console messenger:consume async --time-limit=55 --memory-limit=96M --quiet
```

`--time-limit` sous la minute du cron pour que les processus ne s'empilent pas ; même s'ils se chevauchent, le transport Doctrine verrouille en `SELECT … FOR UPDATE`, donc pas de double envoi. Trois pièges propres au mutualisé : le binaire PHP CLI diffère souvent du PHP web, certains hébergeurs limitent le cron à 5 ou 15 minutes, et il faut un `failure_transport` sinon un mail échoué disparaît sans bruit.

**Mais je vous le déconseille ici.** Les 10 secondes venaient de ma machine, pas de votre code : `MAILER_DSN` pointait sur `localhost:1025` (Mailpit), qui ne tourne pas. Sur votre mutualisé le SMTP est local, un envoi prend quelques dizaines de millisecondes. Messenger ajouterait une table, un cron, un transport d'échec — et surtout un **délai** : si l'hébergeur plafonne le cron à 15 minutes, le lien de réinitialisation arrive un quart d'heure plus tard. Ce serait pire qu'aujourd'hui.

J'ai corrigé la vraie cause : `MAILER_DSN=null://null` par défaut dans `.env`, le contenu des mails reste consultable dans le panneau « Mailer » du profiler, et Mailpit s'active dans `.env.local` pour qui le veut. La production renseigne son SMTP dans `.env.local`, comme `DATABASE_URL`.

À rouvrir le jour où le SMTP est distant et lent, ou quand viendront les notifications de paiement aux planteurs — vous aurez de toute façon déjà un cron pour `app:paiements:reconcilier`, une ligne de plus ne coûtera rien.







Hébergeur ssh grafikart
