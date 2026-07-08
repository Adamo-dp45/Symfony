### Issues

- - 
`Because you have multiple authenticators in firewall "main", you need to set the "entry_point" key to one of your authenticators ("App\Security\AppAuthenticator", "form_login") or a service ID implementing "Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface". The "entry_point" determines what should happen (e.g. redirect to "/login") when an anonymous user tries to access a protected page.`
Étant donné que vous avez plusieurs authentificateurs dans le pare-feu « main », vous devez définir la clé « entry_point » sur l'un de vos authentificateurs (« App\Security\AppAuthenticator », « form_login ») ou sur un ID de service implémentant « Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface ». Le « entry_point » détermine ce qui doit se produire (par exemple, rediriger vers)

# Problème
Dans Symfony, un entry point définit ce qui doit se passer lorsqu'un utilisateur non authentifié tente d'accéder à une page protégée. Si vous avez plusieurs authentificateurs dans votre configuration, Symfony ne sait pas lequel utiliser pour rediriger l'utilisateur. D’où l'erreur demandant de préciser l'option entry_point.
    > Exemple de configuration problématique : Dans security.yaml, si vous avez plusieurs méthodes d'authentification comme custom_authenticator et form_login :
        security:
            firewalls:
                main:
                    lazy: true
                    provider: users
                    custom_authenticator:
                        - App\Security\AppAuthenticator
                    form_login:
                        login_path: login
                        check_path: login

**On choisi un seule moyen et on supprimer l'autre**
- - 

- - 
`Allowed memory size of 134217728 bytes exhausted (tried to allocate 32768 bytes)`
PHP a dépassé la limite de mémoire autorisée pendant l'exécution de la commande `cache:clear`

# Problème
* PHP essaie d'allouer plus de mémoire que ce qui est autorisé
* Cela se produit souvent pendant la génération du cache Twig ou des conteneurs de services, surtout avec de gros projets ou après l'installation de bundles/plugins

# Solution
`php.ini`
    - memory_limit = 128M
    - memory_limit = 512M
Ou `php -d memory_limit=-1 composer require nom-du-plugin` - *Restart PHP server*
- - 

- - 
`Cannot autowire service "App\Security\APIAuthenticator": argument "$userProvider" of method "Symfony\Component\Security\Http\Authenticator\AbstractPreAuthenticatedAuthenticator::__construct()" references interface "Symfony\Component\Security\Core\User\UserProviderInterface" but no such service exists. You should maybe alias this interface to one of these existing services: "security.user.provider.concrete.app_user_provider"`

Symfony essaie d’injecter automatiquement une dépendance vers l’interface UserProviderInterface dans le constructeur de ta classe App\Security\APIAuthenticator, mais il ne sait pas quel service concret utiliser pour cette interface

# Solution
Créer un alias vers le provider existant :
    security.yaml
        security:
            providers:
                app_user_provider:
                    entity:
                        class: App\Entity\User
                        property: email

Symfony a automatiquement créé un service nommé - security.user.provider.concrete.app_user_provider

- Tu dois donc créer un alias vers UserProviderInterface
    config/services.yaml
        services:
            Symfony\Component\Security\Core\User\UserProviderInterface: '@security.user.provider.concrete.app_user_provider'
- - 









- - 
* 
Repo: Build yout own x qui apprend à faire tout soit même créer un langage ou framework
https://pestphp.com/ pour les tests élégentes php
cron qui sauvegarde la bd voir symfony
Temps réel framework frontend
- Guide utilisateur via Driver.js ou Intro.js avec persisantce de l'état sur lequel l'utilisateur est et peut recommencer
    ## Comparaison Driver.js vs Intro.js

    **Driver.js**
    - Plus moderne, léger (~5kb gzip)
    - Highlight visuel élégant — met en surbrillance l'élément ciblé avec un overlay
    - API simple et flexible
    - Pas de dépendances
    - Meilleur pour des guides contextuels par page/module
    - Gratuit et open source

    **Intro.js**
    - Plus ancien, plus lourd
    - Style "tooltip numéroté" classique
    - Nécessite une licence pour usage commercial
    - Plus de configuration nécessaire

    **Recommandation : Driver.js** — plus adapté à ton cas car tu as des guides par module (contextuels par page), il est plus léger, gratuit et son rendu est plus moderne.
* 




# CLAUDE.md

## Aperçu du projet
Application web Symfony 7.x pour [décrire le métier, ex: gestion de commandes e-commerce].
- PHP 8.3
- Doctrine ORM + PostgreSQL
- API Platform pour les endpoints REST
- Templates Twig pour le rendu front (ou React/Vue si SPA)

## Structure clé
- `src/Entity/` — entités Doctrine
- `src/Repository/` — requêtes custom
- `src/Controller/` — contrôleurs (un par ressource)
- `src/Service/` — logique métier, jamais dans les contrôleurs
- `src/Message*/` — Messenger (commandes/handlers async)
- `migrations/` — migrations Doctrine générées

## Conventions de code
- Respecter PSR-12, formaté avec PHP-CS-Fixer
- Typage strict partout (`declare(strict_types=1)`)
- Les entités n'ont pas de logique métier — passer par les Services
- Les DTO pour les entrées/sorties API, jamais exposer les entités directement
- Tests obligatoires pour tout nouveau Service (PHPUnit)

## Commandes fréquentes
```bash
symfony serve                          # serveur de dev
php bin/console doctrine:migrations:migrate   # appliquer migrations
php bin/console make:entity            # créer une entité
vendor/bin/phpunit                     # lancer les tests
vendor/bin/php-cs-fixer fix            # formater le code
```

## Base de données
- Ne jamais modifier une migration déjà appliquée en prod — en créer une nouvelle
- Toujours passer par `make:migration` après modif d'entité, ne pas écrire les migrations à la main

## Zones sensibles (demander confirmation avant modification)
- `src/Security/` — logique d'authentification
- `config/packages/security.yaml`
- Tout ce qui touche au paiement (`src/Service/Payment/`)

## Notes
- Le projet suit une architecture hexagonale légère : ne pas coupler Domain et Infrastructure
- Les webhooks externes sont gérés async via Messenger, pas en synchrone








7Backend et frontend ont `APP_SECRET=` (vide) dans le `.env` committé. `APP_SECRET` signe notamment les jetons CSRF et **le cookie `remember_me`** côté frontend (`security.yaml` : `secret: '%kernel.secret%'`). Avec un secret vide ou connu en production, un attaquant peut **forger un cookie `remember_me`** et se faire passer pour n'importe quel utilisateur → contournement d'authentification.

→ *Correctif : ne jamais committer la valeur de prod. Générer un secret fort par environnement via `.env.local` (déjà gitignoré) ou le coffre Symfony (`php bin/console secrets:set APP_SECRET`). Vérifier que `APP_SECRET` est non vide au démarrage en prod.* C'est du dev pour l'instant, mais à verrouiller **avant** toute mise en ligne.




Repo.. codecrafters-io/build-your-own
awesome-selfhosted/awesome-selfhosted
    CRM, ERP, gestion de stock, chat, trello clones, Notion clones, outils IA, analytics, facturation :: Très bon pour les Sass
awesome-software-architecture
    DDD, CQRS, Event Driven, Hexagonal Architecture, Clean Arch..


- Jeux vidéo(temps réel): C++, C#, Lua, GDScript, GLSL, JavaScript
- Mobile: Kotlin, Swift, Dart, JavaScript
    > Android Cross-plateforme : Flutter via Dart, React Native, C++ via NDK, Python via Kivy, BeeWare ou PyQt pour desktop et limité sur adnroid
- Data & IA: Python, SQL, R, Julia, Scala, Java
- Réseaux télécom : C, C++, Erlang, Go, Python, Java
- Interface - Web frontend : HTML, Css, Javascript, Typescript, GLSL
- Service - Web backend : Typescript, Python, Php, Java, C#, Go, Rust
- Infra - DevOps & Cloud : Bash, Yaml, Python, Go, HCL, Rust
- Matériel - Embarqué & loT : C, C++, Rust, MicroPython, Ada
- Platforme - Système : C, C++, Rust, Go, Assembleur, Zig
    > Desktop Natif : Rust qui remplace C++, C# / .NET pour Windows natif via WPF, WinForms, MAUI lui cible aussi android et iOS depuis .NET 6+
- Entreprise - Logiciel métier : Java, C#, SQL, Python, ABAP, VBA
- Blockchain - Blockchain & Smart contracts : Solidity, Rust, Go, Move, Vyper

- Java Spring permet de faire des applications web, Python avec Django


- Temps réel

Mercure #[ApiResource(
    mercure: true
)]
class Ticket
{
}
À chaque modification d'un ticket, API Platform peut automatiquement publier la mise à jour sur Mercure.
Alternative au polling : du push temps réel via Mercure (SSE) déjà bien intégré à Symfony, ou WebSocket — plus efficace mais plus d'infra. Le polling 10–15 s est le bon point de départ.





**Idée de Sass** Logiciel en ligne que les gens payent par abonnement chaque mois ( Vu par défaut il y'a une agence ou un gérant qui gère l'immeuble donc ils ont un pourcentage dessus ce qui fait que certains proprio gèrent eux même, donc chaque mois c'est le même enfer pour eux )
- La gerants d'immeubles (Sass de gestion d'immeubles)
    - Le problème : Loyers a courrir un par un, facture d'eau et d'éléectricité commune repartie à la calculatrice entre 12 appartements, traveaux géres en especes. Tout au cahier.
    - La solution : Loyers payés en Mobile Money dans l'outil, repartition automatique des factures, traveaux tracés avec photos. Le propriétaire voit tout en temps réel, même depuis Paris
    > 400 immeubles x 10.000F = 4.000.000F
    La fature commune tu l'entre une fois et l'outil la reparti auto entre les appartements et chacun recois pour lui
    Le client en or : le propriétaire de la diaspora
- Les écoles privées
    - Le problème : Courrir après les frais de scolarités : rétards, espaces mal tracées, et des milions jamais recouvres en fin d'années
    - La solution : Un dossier par élève, des échéances, des rappels automatique SMS/Whatsapp, le paiement en Mobile Money. Le directeur voit tout en temps réel
    200 ecoles x 25.000F = 5.000.000F
- Les pharmacies (Sass de gestion des stocks pensées pour les pharmacies)
    - Le problème : Des miliers de references, des dates de peremption partout, des inventaires à la mais et des stocks entiers jetés
    - La solution : Alertes peremption automatiques, suivi des ventes, suggestions de reapprovisionnement. Un outil pensé pharmacie, en français, payé en Mobile money
    15.000F/mois
- Les restaurants (Un peu ce qu'on a fais pour Quincaillerie)
    - Le problème : Les commandes arrives sur Whatsapp entre deux plats, notées sur papier : une sur cinq est oubliée. A midi, c'est la guerre
    - La solution : Un menu digital avec un lien : le client commande, tout tombe dans un tableau de bord. le resto garde ses livreurs, ses clients et sa marge
    15.000F/mois
- Les cliniques et cabinets privée
    - Le problème : Ticket à 8 heurs, trois heures d'attente, et le dossier patient dans une chemise cartonnée au fond d'une armoire
    - La solution : Rendez-vous en ligne, Rappel automatique aux patients, dossier retrouve deux clics. Pas besoin d'un logiciel hospitalier complet.
    20.000F/mois
    Les dossiers dans ton outil : Un client qui ne repart plus
- Les instituts de beauté et salons (de coiffure..)
    - Le problème : La gérante à la main prise, repond 3 heures après, note tout dans un cahier. Un cliente sur cinq ne vient pas : argent perdu
    - La solution : Réservation en ligne, rappel Whatsapp automatique la veille, la journée visible sur le téléphone. Les rendez-vous manqués chutent
    10.000F/mois
    4 rendez-vous sauvez par mois : l'outil est déjà rembourse







Redis pour gérer le système de panier

On vas à developpé une application e-commerce.

Voici les modules que j'ai prélevé :

**Authentification & Sécurité**
- Inscription (email + mot de passe)
- Connexion JWT + Refresh Token
- Vérification email
- Récupération / réinitialisation mot de passe
- 2FA (TOTP — Google Authenticator)
- OAuth (Google, Facebook)
- Inscription / connexion par numéro de téléphone (Twilio)
- Blocage de compte

**Profil utilisateur**
- Informations personnelles (avatar, bio, username)
- Changement d'email (avec vérification)
- Changement de mot de passe
- Gestion des adresses (livraison + facturation, plusieurs adresses)
- Historique des commandes
- Préférences (langue, devise, notifications)
- Abonnement newsletter
- Suppression de compte

**Catalogue produits**
- CRUD produits (nom, slug, description, prix, poids)
- Catégories produits (avec image, slug)
- Attributs personnalisables (taille, couleur, matière…)
- Variantes de produits (ex : taille M + couleur rouge = SKU spécifique)
- Upload multiple d'images et upload de vidéo de produit
- Gestion des stocks (quantité, stock réservé, seuil d'alerte)
- Statut produit (en ligne / hors ligne)
- Produits liés / suggestions
- Tags produits
- Avis et notes (étoiles)

**Recherche & Filtrage**
- Recherche full-text (Meilisearch / Typesense)
- Suggestions en temps réel (autocomplete)
- Filtres : prix, catégorie, marque, disponibilité, note, couleur
- Tri : prix croissant/décroissant, nouveautés, popularité
- Pagination classique + infinite scroll
- Historique de recherche utilisateur
- Produits récemment consultés

**Panier**
- Panier session (invité)
- Panier base de données (utilisateur connecté)
- Fusion des deux à la connexion et à l'inscription
- Ajout / modification / suppression d'articles
- Sauvegarde automatique du panier
- Calcul : sous-total, taxes, frais de livraison, total
- Application de codes de réduction / coupons
- Panier abandonné (rappel par email / notification)

**Commandes & Checkout**
- Processus de commande étape par étape (checkout stepper)
- Commande utilisateur connecté
- Commande invité (formulaire guest)
- Sélection adresse de livraison
- Sélection mode de paiement
- Calcul frais de livraison selon zone
- Récapitulatif avant validation
- Confirmation de commande (email + notification)
- Suivi des statuts : `en préparation → en livraison → livré → annulé`
- Génération facture PDF après livraison
- Annulation de commande

**Paiements**
- Paiement à la livraison
- Paiement en ligne : **Wave, Orange Money, PayPal, Stripe**
- Support multi-devises avec conversion temps réel
- Système de remboursement (partiel / complet)
- Historique des transactions
- Gestion des échecs de paiement

**Livraisons et transporteurs internes** du genre Yango Delivery ou encore Uber Delivery, etc..
- Zones de livraison (nom, coût, limite de poids, frais supplémentaires)
- Calcul automatique des frais selon zone + poids produit
- Intégration transporteurs
- Suivi de colis en temps réel
- Notifications de mise à jour statut livraison (SMS Twilio + email)
- Dans mes recherches j'ai aussi eu des informations comme :
    * Créneaux horaires de livraison
    * Livraison programmée
    * Livraison express
    * Livraison le jour même
    * Gestion des retours
    * Gestion des échecs
    * OTP de validation livraison
    * Signature numérique
    * Scan QR code colis
    * Impression étiquette colis
    * Optimisation automatique itinéraires
    * SLA livraison
    * Dashboard logistique
> Pour ce module je suis un peu confus, explique moi et arrange le

**Wishlist & Fidélisation**
- Wishlist (liste d'envies par utilisateur)
- Système de points de fidélité (gagner à l'achat)
- Système de badges (fidélité, gros acheteur, premier achat…)
- Visualisation des récompenses dans le profil
- Niveaux de fidélité (bronze, argent, or…)

**Commentaires & Avis**
- Avis produits (note étoiles + texte)
- Réponse aux avis (admin / vendeur)
- Commentaires articles de blog
- Réponses imbriquées (nested comments)
- Modération (signalement, validation admin)
- Like / Dislike sur les avis

**Notifications**
- Notifications in-app (temps réel via **Mercure** ou WebSocket)
- Notifications email (confirmation commande, livraison, etc.)
- Notifications SMS (Twilio — commande confirmée, en route)
- Notifications push (PWA)
- Rappel panier abandonné
- Alertes promotions
- Paramètres de notification par utilisateur (ce qu'il veut recevoir)

**Blog**
- CRUD articles (titre, slug, contenu Markdown, image, vidéo)
- Catégories d'articles
- Brouillons / publication
- Commentaires sur articles
- Pagination
- Recherche dans les articles
- Tags

**Newsletter**
- Inscription / désinscription (avec token)
- Envoi d'emails en masse (via queue Symfony Messenger)
- Templates d'emails personnalisables
- Tracking ouvertures et clics
- Historique des campagnes envoyées

**Page d'accueil & UX**
- Produits populaires / récents
- Bannières promotionnelles (CRUD depuis l'admin)
- Section témoignages / avis clients
- Accès rapide aux catégories
- Infos contact (adresse, téléphone, horaires)
- Menu déroulant dynamique avec catégories
- Fil d'Ariane (breadcrumb)

**Tableau de bord Admin**
- Vue d'ensemble : ventes, utilisateurs, commandes du jour
- Gestion utilisateurs (CRUD + rôles : user / admin / super admin)
- Gestion produits, catégories, attributs, stocks
- Gestion commandes (voir, changer statut, rembourser)
- Gestion codes de réduction / coupons
- Gestion zones de livraison
- Gestion bannières promotionnelles
- Modération commentaires et avis
- Alertes stock faible
- Statistiques & graphiques (ventes, revenus, produits top)
- Audit logs (qui a fait quoi)
- Export données (CSV, Excel, PDF)

**Analyse & Reporting**
- Comportement utilisateurs (produits consultés, clics)
- Suivi conversions (Google Analytics 4 / Plausible)
- Statistiques internes (dashboard admin)
- Rapport ventes par période
- Produits les plus vendus
- Revenus par catégorie

**Intégrations externes**
- **Meilisearch** ou **Typesence** (recherche avancée)
- **Twilio** (SMS)
- **Wave / Orange Money / Stripe / PayPal** (paiements)
- **Mailchimp ou Resend** (emails transactionnels et marketing)
- **Google Analytics** (tracking)
- **Google Search Console** (sitemap, SEO)


Donne moi un suggestion pour la stack, moi j'ai pensé à une architecture séparé :
    - Backend : Symfony v8, API Platform v4, LexikJWTBundle, GesdinetRefreshBunlde etc..
    - Frontend : Next.js v16, Tailwind v4, etc..

Pour le développement de l'application je veux qu'on aille étape par étape, module par module...




KnpSnappyBundle (wkhtmltopdf)




Voici un guide complet pour les deux types de mise à jour.

---

## Mise à jour mineure (ex: 6.3 → 6.4)

Une mise à jour mineure ne devrait pas introduire de changements incompatibles significatifs. C'est la mise à jour la plus simple.

### 1. Mettre à jour `composer.json`

Tu dois mettre à jour les contraintes de version pour chaque paquet `symfony/*` :

```json
// composer.json
"require": {
    "symfony/config":  "6.4.*",
    "symfony/console": "6.4.*",
    // ...
}
```

### 2. Lancer Composer

```bash
composer update "symfony/*"
```

### 3. Mettre à jour les recettes Flex

```bash
composer recipes          # lister les recettes obsolètes
composer recipes:update   # mettre à jour interactivement
```

### 4. Vider le cache

```bash
php bin/console cache:clear
```

### 5. Corriger les dépréciations (facultatif mais recommandé)

Chaque version de Symfony inclut un fichier `UPGRADE-X.Y.md` dans le répertoire Symfony décrivant les changements. Consulte-le pour anticiper les prochaines suppressions.

---

## Mise à jour majeure (ex: 6.4 → 7.0)

C'est un processus en **deux grandes étapes**. Il ne faut surtout pas sauter directement à la version majeure.

### Étape 1 — Se préparer sur la dernière version mineure

Toutes les fonctionnalités dépréciées sont supprimées lors de la sortie de la version majeure. Donc, tant que tu as mis à jour ton code pour ne plus utiliser ces fonctionnalités dépréciées dans la dernière version mineure (ex: 6.4.*), tu devrais pouvoir faire la mise à jour sans problème.

**Activer le mode strict des dépréciations** (très utile) :

```php
// config/packages/framework.php ou .yaml
framework:
    php_errors:
        throw: true  # transforme les dépréciations en exceptions
```

Ou via Symfony's DebugBundle, dans `dev` :

```bash
php bin/console debug:container --deprecations
```

Tu peux aussi utiliser **PHPStan** avec l'extension Symfony pour détecter les usages dépréciés statiquement.

### Étape 2 — Passer à la version majeure

**Modifier `composer.json`** — passer de `6.*` à `7.*` :

```json
"require": {
    "php": ">=8.2",
    "symfony/framework-bundle": "7.*",
    "symfony/console": "7.*",
    // tous les autres symfony/*
}
```

> Symfony 7.x requiert PHP 8.2 minimum.

**Lancer la mise à jour :**

```bash
composer update "symfony/*" --with-all-dependencies
```

**Mettre à jour les recettes Flex :**

```bash
composer recipes:update
```

**Vider le cache :**

```bash
php bin/console cache:clear
```

---

## Conseils pratiques

| Situation | Conseil |
|---|---|
| Upgrade majeur | Toujours passer par la dernière mineure d'abord (6.4 avant 7.0) |
| Conflits Composer | Mettre à jour aussi les dépendances tierces (`doctrine/*`, `api-platform/*`, etc.) |
| Bundles tiers | Vérifier leur compatibilité avant d'upgrader Symfony |
| CI/CD | Faire tourner les tests après chaque étape |

Le cycle de Symfony est bien établi : toutes les six versions mineures, la dernière devient une LTS, et sert de "passerelle" vers la prochaine version majeure — toutes les dépréciations y sont concentrées. C'est pour ça que passer par la LTS (ex: 7.4) avant Symfony 8.0 sera la bonne stratégie.






La voici — version Symfony pure, dans `BK-Transport/config/packages/security.yaml`, bloc `access_control` :

```yaml
    access_control:
        # requires_channel: https → Symfony renvoie une redirection vers https si la requête arrive en http
        - { path: ^/api/(login|token/refresh), roles: PUBLIC_ACCESS, requires_channel: https }
        - { path: ^/,                          roles: PUBLIC_ACCESS, requires_channel: https }
```

### ⚠️ Le piège n°1 : « seule la PREMIÈRE règle qui matche s'applique »
C'est écrit dans ton fichier (`# Note: Only the *first* matching rule is applied`). Donc :
- Si tu ajoutes une règle catch-all `^/` avec `requires_channel` **mais que tu ne la mets pas sur ta règle login existante**, les routes `/api/login` matcheront la 1ʳᵉ règle (sans `requires_channel`) et **ne seront pas forcées en HTTPS**.
- → Il faut donc mettre `requires_channel: https` sur **chaque** règle (y compris la ligne login que tu as déjà), comme ci-dessus.

`roles: PUBLIC_ACCESS` sur le catch-all `^/` n'ouvre **pas** ton API : `access_control` est une couche grossière ; la vraie sécurité reste portée par les opérations ApiPlatform (`security: "is_granted(...)"`) et tes `#[IsGranted]`. Ici on ne s'en sert que pour exiger le canal HTTPS.

### ⚠️ Le piège n°2 : derrière un proxy = boucle de redirection infinie
Si l'app est derrière un proxy/load-balancer (cas hébergé classique), le proxy parle HTTP à ton app même quand le client est en HTTPS. Symfony croit alors être en HTTP → redirige vers HTTPS → le proxy renvoie en HTTP → **boucle infinie**. Il faut déclarer les proxys de confiance dans `config/packages/framework.yaml` :

```yaml
framework:
    trusted_proxies: '%env(TRUSTED_PROXIES)%'   # ex. '127.0.0.1,REMOTE_ADDR' ou la plage du LB
    trusted_headers: ['x-forwarded-for', 'x-forwarded-host', 'x-forwarded-proto', 'x-forwarded-port']
```

Avec ça, Symfony lit `X-Forwarded-Proto: https` et sait que la requête est déjà sécurisée → pas de boucle.

### `.htaccess` vs `requires_channel` — lequel choisir
| | `.htaccess` (Apache) | `requires_channel` (Symfony) |
|---|---|---|
| Niveau | Serveur web, **avant** PHP | Application, dans le firewall |
| Coût | Aucun (pas de boot PHP) | Boot Symfony à chaque requête http |
| Portabilité | Apache uniquement | Tout serveur (Nginx, etc.) |
| Granularité | Tout le vhost | Par `path` (HTTPS sur certaines routes seulement) |

**En pratique** : pour forcer HTTPS partout, le `.htaccess` (ou la conf Nginx) est le plus efficace car la redirection se fait avant même PHP. Garde `requires_channel` pour un besoin fin (forcer HTTPS sur quelques routes précises). Les deux sont compatibles — pas besoin des deux pour le même périmètre.





- Je peut lui envoyer ma doc ApiPlatform et lui demander de créer un client react
    > $env:NODE_TLS_REJECT_UNAUTHORIZED="0"; npm init @api-platform/client https://127.0.0.1:8000/api src/ -- --generator react

    
## Côté JavaScript / TypeScript

### Option 1 — `api-platform/client-generator` (officiel)
Il génère automatiquement des composants React, Next.js, Vue ou Nuxt à partir de votre API



login_link




- Les ValueResolver : permet de gérer automatiquement l'hydratation des éléments et des paramètres qu'on a dans notre controller `RecipeController`, on a `MapQueryString`, `MapRequestPayload`, `MapHydratedEntity`, `MapEntity`..


JoliMediaBundle: upload, redimessionnement, gallery..
twig components, live components qui met en place des éléments réactif sans écrire du js
On a un typesence bundle qui permet d'intéragir avec son serveur pour symfony, on peut utiliser live component avec ce bundle pour une recherche en live
SchebTwoFactorBundle : Pour l'auth à 2 facteurs
    > composer require 2fa
    > !! scheb/2fa-email : Une ext de ce bundle pour faire l'auth à 2fa par email
    > ..
https://api-platform.com/docs/symfony/file-upload/#uploading-to-an-existing-resource-with-its-fields : Pour l'upload

    public const ACTIONS = ['VIEW', 'CREATE', 'EDIT', 'DELETE'];
    #[Assert\Choice(choices: self::ACTIONS, message: "Action invalide. Valeurs : VIEW, CREATE, EDIT, DELETE.")]





            // ->get('roles')->addModelTransformer(new RolesToStringTransformer());



->add('imageFile', FileType::class, [
    'label' => 'Logo',
    'required' => false,
    'mapped' => false,
    'constraints' => [
        new File([
            'maxSize' => '1024k',
            'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'],
            'mimeTypesMessage' => 'Veuillez uploader une image valide (JPEG, PNG, GIF)',
        ])
    ],
])

$imageFile = $form->get('imageFile')->getData();

- New
if ($imageFile) {
    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
    $safeFilename = $this->slugger->slug($originalFilename);
    $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

    try {
        $imageFile->move(
            $this->uploadDirectory.'/entreprises',
            $newFilename
        );
        $entreprise->setImage($newFilename);
    } catch (\Exception $e) {
        $this->addFlash('error', 'Erreur lors de l\'upload du logo');
    }
}

- Edit
$imageFile = $form->get('imageFile')->getData();
if ($imageFile) {
    // Supprimer l'ancien logo si existe
    if ($entreprise->getImage()) {
        $oldImage = $this->uploadDirectory.'/entreprises/'.$entreprise->getImage();
        if (file_exists($oldImage)) {
            unlink($oldImage);
        }
    }

    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
    $safeFilename = $this->slugger->slug($originalFilename);
    $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

    try {
        $imageFile->move(
            $this->uploadDirectory.'/entreprises',
            $newFilename
        );
        $entreprise->setImage($newFilename);
    } catch (\Exception $e) {
        $this->addFlash('error', 'Erreur lors de l\'upload du logo');
    }
}



**Historique de connexion** - On a crée `LoginListener` pour enregistrer les connexions

**Authentification Api dans une architecture séparée**
- On a 2 stratégies en architecture séparée
    > Sol 1 : L'authentification par cookies `stateful` recommandée pour `SPA`
        > Le backend crée une session, le navigateur stocke un cookie `HttpOnly` et le cookie est envoyé automatiquement à chaque requête
        > L'inconvénient est qu'il nécessite une configuration `CORS` et `CSRF` mais géré automatiquement par le backend
    > Sol 2 : !! par tokens `stateless` recommandée pour `mobile`, `api public` ou `microservice`
        > Le backend génère un token `jwt`, le frontend le stocke dans un `localStorage` ou `memory` et il est envoyé dans le header `Authorization`
        > Les inconvénients : Le.. vulnérable au `XSS`, gestion expiration complexe et logout plus compliqué


datatracker.ietf.org : Contient les information sur les grandes notions ex: jwt, oauth..
#[CurrentUser] User $user permet de récupérer l'utilisateur connecté

Pour modifier l'état de l'application lors des opérationsPOST de chargement , PUTde déchargement PATCHou de réplication , API Platform utilise des classes appelées processeurs d'état . Ces processeurs reçoivent une instance de la classe marquée comme ressource API (généralement à l'aide de l' attribut `state`). Cette instance contient les données soumises par le client lors du processus de désérialisation .DELETE #[ApiResource]

php bin/console make:state-processor UserPasswordHasher : Pour créer un processor `post`, `put` ou `patch`

Il n'existe pas de méthode intégrée pour hacher le mot de passe en clair sur POST, PUTou PATCH. Heureusement, vous pouvez utiliser les processeurs d'état de la plateforme API pour hacher automatiquement les mots de passe en clair.

Avoir une propriété `plainPassword` comme un champ temporaire et n'est pas une colonne à persisté, crée un processeur `UserPasswordHasher`


- **Client-Server**
    > Pour la partie `server`
        > composer require api : Pour `ApiPlatform`
        > composer require symfony/orm-pack : Pour la base de données avec `Doctrine`
        > composer require --dev symfony/maker-bundle : !! générer les entités
        > composer require symfony/security-bundle : Pour l'authentification `o`
        > composer require lexik/jwt-authentication-bundle : Pour le `jwt`
        > composer require gesdinet/jwt-refresh-token-bundle
        > composer require nelmio/cors-bundle : Pour les domaines séparés `o`
        > composer require symfony/validator : !! valider les données `o`
        > composer require symfony/serializer : `o`
        composer require symfony/serializer-pack : souvent utile avec API Platform

Le `sylius.com` permet de créer un ecommerce plus facilement avec symfony

Les options définies ci-dessous defaultsconstituent les valeurs par défaut appliquées à tous les pathséléments correspondants, sauf si elles sont remplacées dans une configuration d'URL spécifique. Si vous souhaitez qu'elles s'appliquent à tous les éléments, vous devez définir un chemin avec ` ^/.`.

Cette configuration d'exemple contient toutes les valeurs possibles, leurs valeurs par défaut étant indiquées dans la defaultslégende. Dans la section « chemins », vous pouvez constater que les requêtes CORS sont autorisées depuis n'importe quelle origine /api/. Un en-tête personnalisé et certaines méthodes HTTP sont également autorisés. Les requêtes préliminaires peuvent être mises en cache pendant 3 600 secondes.

nelmio_cors:
    defaults:
        allow_credentials: false
        allow_origin: []
        allow_headers: []
        allow_methods: []
        allow_private_network: false
        expose_headers: []
        max_age: 0
        hosts: []
        origin_regex: false
        forced_allow_origin_value: ~
        skip_same_as_origin: true
    paths:
        '^/api/':
            allow_origin: ['*']
            allow_headers: ['X-Custom-Auth']
            allow_methods: ['POST', 'PUT', 'GET', 'DELETE']
            max_age: 3600
        '^/':
            origin_regex: true
            allow_origin: ['^http://localhost:[0-9]+']
            allow_headers: ['X-Custom-Auth']
            allow_methods: ['POST', 'PUT', 'GET', 'DELETE']
            max_age: 3600
            hosts: ['^api\.']









L'attribut `#[ORM\HasLifecycleCallbacks]` dans Symfony (plus précisément dans Doctrine ORM) est utilisé pour **activer les méthodes de cycle de vie** dans une entité. 

## 🎯 À quoi ça sert ?

Cet attribut permet d'exécuter automatiquement des méthodes spécifiques à différents moments du cycle de vie d'une entité Doctrine, comme :

## 🔄 Les principaux callbacks disponibles :

### 1. **`#[ORM\PrePersist]`**
Exécuté AVANT qu'une entité soit persistée pour la première fois (INSERT)
```php
#[ORM\PrePersist]
public function setCreatedAt(): void
{
    $this->createdAt = new \DateTimeImmutable();
}
```

### 2. **`#[ORM\PreUpdate]`**
Exécuté AVANT qu'une entité soit mise à jour (UPDATE)
```php
#[ORM\PreUpdate]
public function setUpdatedAt(): void
{
    $this->updatedAt = new \DateTimeImmutable();
}
```

### 3. **`#[ORM\PostPersist]`**, **`#[ORM\PostUpdate]`**, **`#[ORM\PostRemove]`**
Exécutés APRÈS les opérations correspondantes

### 4. **`#[ORM\PreRemove]`**
Exécuté AVANT la suppression d'une entité

### 5. **`#[ORM\PostLoad]`**
Exécuté APRÈS le chargement d'une entité depuis la base de données

## 📝 Exemple complet :

```php
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;
    
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;
    
    // S'exécute avant le premier persist
    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }
    
    // S'exécute avant chaque update
    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
    
    // S'exécute après chaque chargement
    #[ORM\PostLoad]
    public function logLoading(): void
    {
        // Logique après chargement...
    }
}
```

## ⚠️ Points importants :

1. **L'attribut doit être placé sur la classe** (pas sur une méthode)
2. Les méthodes de callback **ne doivent PAS recevoir d'arguments**
3. Elles doivent **retourner `void`**
4. Doctrine **ignore les méthodes privées/protégées** (utilisez `public`)
5. L'ordre d'exécution n'est pas garanti entre plusieurs méthodes du même type

## 🔄 Alternative : Les Event Listeners/Subscribers

Pour des opérations plus complexes ou réutilisables, vous pouvez utiliser :
- **Event Listeners** : Écouteurs d'événements Doctrine
- **Event Subscribers** : Abonnés aux événements Doctrine

Ces alternatives sont plus flexibles et séparées de l'entité elle-même.

## 📊 Quand utiliser `HasLifecycleCallbacks` ?

✅ **À utiliser pour :**
- Logique simple et spécifique à l'entité
- Mise à jour de timestamps (createdAt, updatedAt)
- Calcul de valeurs dérivées
- Validation simple

❌ **À éviter pour :**
- Logique métier complexe
- Opérations qui nécessitent des services externes
- Logique réutilisable sur plusieurs entités

**En résumé :** Cet attribut est un mécanisme pratique pour exécuter automatiquement du code lors des événements clés du cycle de vie d'une entité Doctrine, directement dans l'entité elle-même.











Option à ajouter dans security.yaml

Symfony cache les erreurs par défaut pour éviter qu’on sache si l’email existe ou non. Ça peut faire apparaître “Invalid credentials” même quand l’email est introuvable.

Si tu veux des erreurs plus claires :

security:
    expose_security_errors: all


Ça peut t’aider à débugger pendant le développement.








Pour cette propriété avec `DATE_IMMUTABLE` dans Symfony FormBuilder, vous devriez utiliser `DateType` avec l'option `'input'` configurée pour `'datetime_immutable'`.

Voici les options recommandées :

```php
use Symfony\Component\Form\Extension\Core\Type\DateType;

// Dans votre FormType
$builder->add('anneecreation', DateType::class, [
    'widget' => 'single_text', // Pour avoir un input type="date"
    'format' => 'yyyy-MM-dd',  // Format recommandé pour single_text
    'input' => 'datetime_immutable', // IMPORTANT pour DateTimeImmutable
    'required' => false, // Car nullable=true
    'label' => 'Année de création',
    // Autres options possibles :
    'html5' => true, // Pour utiliser le sélecteur de date natif du navigateur
]);
```

**Explications :**

1. **`'input' => 'datetime_immutable'`** : C'est l'option la plus importante. Elle indique au FormType que la donnée doit être traitée comme un objet `DateTimeImmutable`.

2. **`'widget' => 'single_text'`** : Utilise un seul champ input HTML (type="date") au lieu de trois champs séparés (jour, mois, année).

3. **`'required' => false`** : Correspond à `nullable=true` dans l'entité.

4. **`'html5' => true`** : Active le sélecteur de date natif du navigateur (recommandé).

**Alternative si vous voulez seulement l'année :**

Si vous ne voulez que l'année (comme le nom de la propriété semble l'indiquer), vous pouvez utiliser :

```php
use Symfony\Component\Form\Extension\Core\Type\DateType;

$builder->add('anneecreation', DateType::class, [
    'widget' => 'choice',
    'format' => 'yyyy', // Format année seulement
    'years' => range(date('Y') - 100, date('Y')), // Plage d'années (100 dernières années)
    'input' => 'datetime_immutable',
    'required' => false,
    'label' => 'Année de création',
    // Optionnel : masquer jour et mois
    'days' => false,
    'months' => false,
]);
```

**Note importante :** Le type `DATE_IMMUTABLE` en base de données stocke la date complète (jour/mois/année), même si vous ne capturez que l'année dans le formulaire. Le jour et le mois seront généralement définis sur le 1er janvier par défaut.






#[ORM\Table(name: '`user`')] : Pour indiquer le nom d'une table

#[Route(env: ['dev', 'test'])] : Indique que la route existe que pour l'environnement de `dev` ou `test`









formulaires multi-étapes


use Symfony\Component\Form\Flow\AbstractFlowType;

class UserSignUpType extends AbstractFlowType
{
    public function buildFormFlow(FormFlowBuilderInterface $builder, array $options): void
    {
        $builder->addStep('personal', UserSignUpPersonalType::class);
        $builder->addStep('professional', UserSignUpProfessionalType::class);
        $builder->addStep('account', UserSignUpAccountType::class);

        $builder->add('navigator', NavigatorFlowType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserSignUp::class,
            // `$currentStep` is a property defined in the UserSignUp class
            'step_property_path' => 'currentStep',
        ]);
    }
}


class UserSignUpController extends AbstractController
{
    #[Route('/signup')]
    public function __invoke(Request $request): Response
    {
        $flow = $this->createForm(UserSignUpType::class, new UserSignUp())
            ->handleRequest($request);

        if ($flow->isSubmitted() && $flow->isValid() && $flow->isFinished()) {
            // do something with $form->getData()

            return $this->redirectToRoute('app_signup_success');
        }

        return $this->render('signup/flow.html.twig', [
            'form' => $flow->getStepForm(),
        ]);
    }
}

Il n'y a que deux différences principales :

Vous devez ajouter une $flow->isFinished()vérification pour savoir quand le formulaire a été marqué comme terminé (par exemple, lorsque l'utilisateur clique sur le bouton de soumission final) ;
Lors du rendu du formulaire, vous devez appeler la fonction $flow->getStepForm()permettant de créer le formulaire pour l'étape en cours.




Les flux de formulaires introduisent une convention utile pour la validation : ils définissent le nom de l’étape en cours comme groupe de validation actif. Utilisez-la dans vos contraintes de validation :


class UserSignUp
{
    public function __construct(
        #[Valid(groups: ['personal'])]
        public Personal $personal  = new Personal(),

        #[Valid(groups: ['professional'])]
        public Professional $professional = new Professional(),

        #[Valid(groups: ['account'])]
        public Account $account = new Account(),

        public string $currentStep = 'personal',
    ) {
    }
}

La navigation à travers les étapes du formulaire est gérée à l'aide des types de boutons suivants, qui étendentButtonFlowType :

ResetFlowType: renvoie le formulaire à plusieurs étapes à l'état initial
NextFlowType: fait passer le formulaire à l'étape suivante
PreviousFlowType: déplace le formulaire à l'étape précédente
FinishFlowType: réinitialise le formulaire et le marque comme terminé
Ces boutons offrent des options telles que skip, back_to, include_if, et clear_submissionpermettent d'activer des étapes conditionnelles, une navigation avancée, etc. Grâce à ces outils, vous pouvez créer des formulaires complexes à plusieurs étapes comme celui ci-dessous :







    /*
        #[CurrentUser] injecte directement l’utilisateur connecté via le token JWT.

Cette route sera protégée par ton firewall JWT (tu devras configurer lexik/jwt-authentication-bundle ou symfonycasts/jwt-auth).

Pas besoin de formulaire : ton frontend envoie username + password à /api/login, le JWT est généré, et ensuite ton controller peut renvoyer les infos utilisateur.

    #[Route(path: '/api/login', name: 'api_login', methods: ['POST'])]
    public function jsonLogin(#[CurrentUser] ?User $user): JsonResponse
    {
        if (!$user) {
            return $this->json(['error' => 'Invalid credentials'], 401);
        }

        return $this->json([
           'username' => $user->getUserIdentifier(),
           'roles' => $user->getRoles()
        ]);
    }


    */



Un Filter est un mécanisme qui permet d’appliquer automatiquement des conditions SQL à toutes les requêtes sur une entité ou un groupe d’entités
C’est un peu comme un WHERE global que tu peux activer ou désactiver selon le contexte.

Exemple concret :

Tu veux que tous les objets User retournés soient uniquement actifs (etat = 1) sans avoir à ajouter ->where('etat = 1') dans chaque requête.

Tu définis un Filter active_user et Doctrine l’ajoute automatiquement à toutes les requêtes sur User.

class ActiveUserFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        // On applique le filtre uniquement sur l'entité User
        if ($targetEntity->getName() !== \App\Entity\User::class) {
            return "";
        }

        // Retourne la clause SQL à ajouter automatiquement
        return sprintf('%s.etat = 1', $targetTableAlias);
    }
}

$targetTableAlias : alias SQL de la table générée par Doctrine.

$targetEntity : la classe de l’entité en cours.

Retourner une string vide "" signifie : pas de filtre appliqué.

Étape 2 : Déclarer le Filter

Dans doctrine.yaml :

doctrine:
    orm:
        entity_managers:
            default:
                filters:
                    active_user:
                        class: App\Doctrine\Filter\ActiveUserFilter
                        enabled: false


enabled: false → le filter n’est pas activé par défaut.

Étape 3 : Activer le Filter

Dans ton code (Controller, Service, etc.) :

$em = $this->getDoctrine()->getManager();
$em->getFilters()->enable('active_user'); // active le filter


Tu peux aussi désactiver :

$em->getFilters()->disable('active_user');

Étape 4 : Ajouter des paramètres dynamiques (optionnel)

Parfois, tu veux que le filtre prenne une valeur dynamique :

$filter = $em->getFilters()->enable('active_user');
$filter->setParameter('current_user_id', 42);


Et dans le filter :

return sprintf('%s.user_id = %s', $targetTableAlias, $this->getParameter('current_user_id'));

Avantages

Réutilisable : tu définis le filtre une fois, il s’applique partout.

Sécurité : idéal pour les règles globales comme “soft delete”, “multi-tenant”, “only active”.

Flexible : tu peux activer/désactiver à la volée, ou passer des paramètres.

4️⃣ Cas typiques d’usage

Soft delete : ne retourner que les entités non supprimées (deleted_at IS NULL).

Multi-tenant : filtrer par entreprise_id.

Utilisateurs actifs : filtrer par etat = 1.

Restrictions de visibilité : filtrer selon le rôle de l’utilisateur connecté.

💡 Principe clé : un filter n’écrit pas de requête à ta place, il injecte automatiquement du SQL dans chaque requête Doctrine pour respecter certaines règles globales.

# 🚫 2️⃣ Filtrer automatiquement les éléments supprimés

Pour ne **jamais retourner les éléments supprimés**, on crée un **Doctrine Filter**.

### `src/Doctrine/Filter/SoftDeleteFilter.php`

```php
namespace App\Doctrine\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class SoftDeleteFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $meta, $alias)
    {
        if (!$meta->hasField('deletedAt')) {
            return '';
        }

        return sprintf('%s.deleted_at IS NULL', $alias);
    }
}
```

### Activer dans `doctrine.yaml`

```yaml
doctrine:
    orm:
        filters:
            soft_delete:
                class: App\Doctrine\Filter\SoftDeleteFilter
                enabled: true
```

💥 Tous les `SELECT` ignoreront les éléments en corbeille.

---

# 🧠 3️⃣ Créer un Processor pour la mise en corbeille

```php
// src/State/SoftDeleteProcessor.php
namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;

class SoftDeleteProcessor implements ProcessorInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (method_exists($data, 'setDeletedAt')) {
            $data->setDeletedAt(new \DateTimeImmutable());
            $this->em->flush();
        }

        return $data;
    }
}
```

---

# 🌍 4️⃣ Ajouter la route `/remove` dans l’entité

Exemple avec `Typepiece` :

```php
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\State\SoftDeleteProcessor;

#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),

        new Post(
            uriTemplate: '/typepieces/{id}/remove',
            processor: SoftDeleteProcessor::class,
            security: "is_granted('SUPPRIMER', object)"
        )
    ]
)]
class Typepiece
{
    // ...
}
```

---

# 📩 Appel API

```http
POST /api/typepieces/5/remove
Authorization: Bearer TOKEN
```

### Résultat

✔ L’enregistrement reste en base
✔ `deletedAt` est rempli
✔ Il disparaît des listes normales
✔ Permissions respectées via ton `PermissionVoter`

---

# ♻️ BONUS — Route pour restaurer

```php
// Processor Restore
class RestoreProcessor implements ProcessorInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $data->setDeletedAt(null);
        $this->em->flush();
        return $data;
    }
}
```

Route :

```php
new Post(
    uriTemplate: '/typepieces/{id}/restore',
    processor: RestoreProcessor::class,
    security: "is_granted('MODIFIER', object)"
)
```

---

# 🎯 Résultat final

| Action        | URL                           | Effet                   |
| ------------- | ----------------------------- | ----------------------- |
| Supprimer     | POST /typepieces/{id}/remove  | Mise en corbeille       |
| Restaurer     | POST /typepieces/{id}/restore | Retire de la corbeille  |
| Liste normale | GET /typepieces               | N’affiche pas supprimés |

---

Si tu veux, on peut maintenant :
👉 Ajouter une route `/trash` pour voir uniquement les éléments supprimés
👉 Ou appliquer ça automatiquement à toutes les entités du projet.











```php

/*
    - Si l’autoconfiguration est activée, l' extension sera automatiquement injectée dans Twig. Sinon, ajoute dans `config/services.yaml`
        services:
            App\Twig\BreadcrumbExtension:
                tags: ['twig.extension']
*/



twilio.com/
composer require symfony/twilio-notifier
DSN: twilio://SID:TOKEN@default?from=FROM
Webhook support: Yes
TWILIO_DSN=twilio://SID:TOKEN@default?from=FROM


# config/packages/notifier.yaml
framework:
    notifier:
        texter_transports:
            twilio: '%env(TWILIO_DSN)%'



// src/Controller/SecurityController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\TexterInterface;
use Symfony\Component\Routing\Attribute\Route;

class SecurityController
{
    #[Route('/login/success')]
    public function loginSuccess(TexterInterface $texter): Response
    {
        $options = (new ProviderOptions())
            ->setPriority('high')
        ;

        $sms = new SmsMessage(
            // the phone number to send the SMS message to
            '+1411111111',
            // the message
            'A new login was detected!',
            // optionally, you can override default "from" defined in transports
            '+1422222222',
            // you can also add options object implementing MessageOptionsInterface
            $options
        );

        $sentMessage = $texter->send($sms);

        // ...
    }
}












    App\Listener\ImageCacheSubscriber:
        tags:
        - { name: doctrine.event_subscriber }



<?php

namespace App\Listener;

use App\Entity\Picture;
use App\Entity\Property;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ImageCacheSubscriber implements EventSubscriber
{

    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var UploaderHelper
     */
    private $uploaderHelper;

    public function __construct(CacheManager $cacheManager, UploaderHelper $uploaderHelper)
    {
        $this->cacheManager = $cacheManager;
        $this->uploaderHelper = $uploaderHelper;
    }

    public function getSubscribedEvents()
    {
        return [
            'preRemove',
            'preUpdate'
        ];
    }

    public function preRemove(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        if (!$entity instanceof Picture) {
            return;
        }
        $this->cacheManager->remove($this->uploaderHelper->asset($entity, 'imageFile'));
    }

    public function preUpdate(PreUpdateEventArgs $args) {
        $entity = $args->getEntity();
        if (!$entity instanceof Picture) {
            return;
        }
        if ($entity->getImageFile() instanceof UploadedFile) {
            $this->cacheManager->remove($this->uploaderHelper->asset($entity, 'imageFile'));
        }
    }

}











Ce que tu vois s’appelle "Google One Tap" ou "Google Identity Services".
C’est un système de connexion proposé automatiquement par Google dès que tu visites un site web qui l’a activé.

🔍 Pourquoi ça apparaît ?

Tu as déjà utilisé un compte Google sur ton téléphone.

Le site que tu visites a intégré Google One Tap Login.

Google détecte que tu n’es pas connecté et te propose la connexion rapide.


📌 Comment ça fonctionne ?

En bas (ou en haut) de l’écran, une petite bannière apparaît :
➡️ “Se connecter avec Google”
Tu appuies dessus, et le site reçoit ton email + une clé d’identification (pas ton mot de passe).

🔒 Est-ce sécurisé ?

Oui, Google ne partage pas ton mot de passe.
C’est une méthode moderne d’authentification appelée OAuth 2.0, très utilisée et sécurisée.

📴 Tu veux que ça ne s’affiche plus ?

Je peux te donner la procédure selon ton navigateur (Chrome, Firefox, Safari, etc.) et ton téléphone (Android ou iPhone).
Tu veux désactiver cette bannière ? Ou comprendre comment l’intégrer dans un site web ?









Voici la méthode complète et moderne pour intégrer Google One Tap (Google Identity Services) dans une application Symfony.
C’est la nouvelle API officielle depuis 2023–2024, qui remplace l’ancien Google Sign-In.


---

✅ 1. Créer un identifiant OAuth sur Google Cloud

1. Va sur : https://console.cloud.google.com (pas besoin de le dire dans le code)


2. Crée un projet ou utilise-en un existant


3. Active Google Identity Services


4. Va dans APIs & Services → Identifiants


5. Clique sur Créer un identifiant OAuth 2.0


6. Type : Client ID Web (OAuth 2.0)


7. Ajoute :

Authorized JavaScript origins : https://ton-domaine.com

Authorized redirect URIs : https://ton-domaine.com/connect/google/check (ou ton route Symfony)



8. Récupère ton CLIENT_ID




---

✅ 2. Installer un bundle OAuth dans Symfony

Tu peux utiliser KnpUOAuth2ClientBundle, simple et efficace.

composer require knpuniversity/oauth2-client-bundle league/oauth2-google


---

✅ 3. Configurer Symfony (security + OAuth)

👉 config/packages/knpu_oauth2_client.yaml

knpu_oauth2_client:
    clients:
        google:
            type: google
            client_id: '%env(GOOGLE_CLIENT_ID)%'
            client_secret: '%env(GOOGLE_CLIENT_SECRET)%'
            redirect_route: connect_google_check
            redirect_params: {}

👉 Dans .env

GOOGLE_CLIENT_ID=xxx.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=xxx


---

✅ 4. Configurer ton firewall Symfony

👉 config/packages/security.yaml

security:
    providers:
        app_user_provider:
            entity:
                class: App\Entity\User
                property: email

    firewalls:
        main:
            anonymous: true
            provider: app_user_provider
            logout:
                path: app_logout

            # Route appelée après réception du token Google One Tap
            custom_authenticators:
                - App\Security\GoogleAuthenticator


---

✅ 5. Créer l'authenticator Google

👉 src/Security/GoogleAuthenticator.php

Voici une version complète compatible One Tap :

<?php

namespace App\Security;

use KnpU\OAuth2ClientBundle\Client\Provider\GoogleClient;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class GoogleAuthenticator extends OAuth2Authenticator
{
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new Response('Auth required', Response::HTTP_UNAUTHORIZED);
    }

    public function supports(Request $request): ?bool
    {
        return $request->getPathInfo() === '/connect/google/check';
    }

    public function authenticate(Request $request)
    {
        /** @var GoogleClient $client */
        $client = $this->fetchAccessToken($this->getClient());

        $googleUser = $client->fetchUserFromToken($client);

        return new SelfValidatingPassport(
            new UserBadge($googleUser->getEmail(), function () use ($googleUser) {
                // Créer/charger un utilisateur Symfony
                return ...;
            })
        );
    }

    public function getClient(): GoogleClient
    {
        return $this->clientRegistry->getClient('google');
    }
}

Tu dois juste compléter la création d'utilisateur si c'est un nouvel email.


---

✅ 6. Ajouter la route callback

👉 config/routes.yaml

connect_google_check:
    path: /connect/google/check


---

✅ 7. Ajouter One Tap sur ta page login

👉 Dans ton template Twig (ex: login.html.twig)

<script src="https://accounts.google.com/gsi/client" async defer></script>
<div id="g_id_onload"
     data-client_id="{{ google_client_id }}"
     data-context="signin"
     data-ux_mode="redirect"
     data-login_uri="{{ path('connect_google_check') }}"
     data-itp_support="true">
</div>

<div class="g_id_signin" data-type="standard"></div>

Dans ton contrôleur :

public function login(): Response
{
    return $this->render('security/login.html.twig', [
        'google_client_id' => $_ENV['GOOGLE_CLIENT_ID']
    ]);
}


---

🎉 Résultat

🔥 Dès qu’un utilisateur visite ta page et qu’il est déconnecté :

Google One Tap s’affiche automatiquement en bas ou en haut

L’utilisateur clique → Google envoie le token → Symfony authentifie

Si l’utilisateur existe → connexion

Sinon → création automatique


C’est exactement le comportement des sites modernes.


---

Tu veux que je te génère :

✅ le code complet de l’entité User compatible Google ?
✅ un authenticator déjà prêt avec création auto de compte ?
✅ le template final complet ?

Dis-moi et je te fais ça sur mesure pour ton app Symfony.









Parfait !
Si tu as déjà intégré Google Login avec KnpUOAuth2ClientBundle, il ne te manque que l’ajout de Google One Tap côté front + la route de réception du token.

👉 Bonne nouvelle : pas besoin de modifier ton backend !
Google One Tap utilise la même route /connect/google/check et la même logique OAuth que ton login Google actuel.

Je te donne uniquement ce qui te manque.


---

✅ 1. Ajouter le JS One Tap dans ton template (Twig)

Dans ton template (ex : base.html.twig ou login.html.twig), ajoute :

<script src="https://accounts.google.com/gsi/client" async defer></script>

<div id="g_id_onload"
     data-client_id="{{ google_client_id }}"
     data-context="signin"
     data-ux_mode="redirect"
     data-login_uri="{{ path('connect_google_check') }}"
     data-auto_prompt="true">
</div>

📌 Explication :

data-ux_mode="redirect" → Google te renvoie vers /connect/google/check

data-auto_prompt="true" → active automatiquement One Tap (la bande en bas de l’écran)

data-login_uri → ta route Symfony déjà gérée par KNP



---

✅ 2. Passer ton CLIENT_ID depuis ton contrôleur

Dans ton contrôleur de login :

public function login(): Response
{
    return $this->render('security/login.html.twig', [
        'google_client_id' => $_ENV['GOOGLE_CLIENT_ID'],
    ]);
}


---

✅ 3. Aucun changement backend nécessaire

Ton authenticator KNP fonctionne pour le login normal ET pour One Tap.
Google enverra un credential JWT à /connect/google/check → KNP → GoogleProvider → utilisateur connecté.


---

✔️ Résultat

En intégrant seulement ce code :

Une bannière Google One Tap affichée automatiquement en bas de la page

L’utilisateur clique → redirection → authenticator Google KNP

Aucun changement dans tes routes ou ta logique d’auth


C’est exactement le comportement que tu veux.


---

📌 Tu veux aussi :

la version popup de One Tap ?

le mode headless (sans UI) ?

faire apparaître One Tap sur toutes les pages ?


Je peux t’ajouter ça aussi si tu veux.








































    {#
        {% if property.video %}
        <video width="400" controls>
            <source src="{{ asset('uploads/properties/videos/' ~ property.video) }}" type="video/mp4">
            Votre navigateur ne supporte pas la lecture de vidéo.
        </video>
        {% endif %}
    #}




            ->add('video', FileType::class, [
                'label' => 'Vidéo (mp4, max 10MB)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => [
                            'video/mp4',
                            'video/webm',
                            'video/ogg',
                            'video/*',
                            'video/x-msvideo',
                            'video/quicktime',
                            'video/webm'
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une vidéo valide (mp4, webm, ogg)',
                    ])
                ],
            ])

                    <div class="col">{{ form_row(form.video) }}</div>

    private function attachVideo($video, Property $property)
    {
        if($video === null) {
            return;
        }
        $filename = uniqid("", true) . '.' . $property->getId() . '.' . $video->getClientOriginalExtension();

        try {
            $video->move(
                $this->getParameter('property_videos_dir'), // config/services.yaml
                $filename
            );
        } catch (FileException $e) {
            $this->addFlash('danger', 'Erreur lors de l’upload de la vidéo');
        }

        if ($property->getVideo() && file_exists($this->getParameter('property_videos_dir').'/'.$property->getVideo())) {
            unlink($this->getParameter('property_videos_dir').'/'.$property->getVideo());
        }
        $property->setVideo($filename);
        /* @var UploadedFile $video
        $video = $form->get('video')->getData(); */
        // $this->attachVideo($video, $property);
    }




parameters:
    video.directory: '%kernel.project_dir%/public/uploads/videos'
    video.thumbs.directory: '%kernel.project_dir%/public/uploads/videos/thumbs'



C'est prêt — j'ai créé un guide complet et le code dans le document **"Système D'upload Vidéo Symfony - Guide Complet"** à côté de la conversation. Ouvre-le pour retrouver tout le code (Entity, Form, Controller, Service, templates, JS) et les instructions d'installation (ffmpeg, permissions, paramètres).

Tu veux que je :

* génère les getters/setters et la migration complète ?
* ajoute une version asynchrone (Messenger + worker) pour la génération de vignettes et transcodage ?
* ou transforme l'upload pour supporter les chunks/resumable (tus.io) ?


# Système d'upload vidéo complet pour Symfony

Ce document contient un guide pas-à-pas et le code pour implémenter un système d'upload de vidéos complet dans une application **Symfony** (6+). Il couvre :

* installation des dépendances
* entity `Video` (Doctrine)
* FormType et validation
* Controller pour upload, listing et lecture
* Service `VideoManager` pour stockage, génération de vignettes (thumbnail) et transcodage via **ffmpeg**
* Twig templates (upload form, gallery, player)
* JS minimal pour progrès d'upload (XHR / Fetch)
* Sécurité et bonnes pratiques

> Hypothèses : Symfony 6+, PHP 8.1+, Doctrine ORM, Twig.

---

## 1) Installation et dépendances

Exemples de commandes à exécuter dans le projet :

```bash
composer require symfony/asset symfony/orm-pack symfony/maker-bundle symfony/validator symfony/form twig
# Optional pour upload helpers (facultatif) :
# composer require vich/uploader-bundle
```

Installer ffmpeg sur le serveur (Ubuntu exemple) :

```bash
sudo apt update
sudo apt install ffmpeg
ffmpeg -version
```

---

## 2) Configuration (parameters)

```yaml
# config/services.yaml
parameters:
    videos_directory: '%kernel.project_dir%/public/uploads/videos'
    videos_thumbs_directory: '%kernel.project_dir%/public/uploads/videos/thumbs'
```

Créez les dossiers et donnez les permissions :

```bash
mkdir -p public/uploads/videos public/uploads/videos/thumbs
chmod -R 775 public/uploads/videos
```

---

## 3) Entity `Video`

```php
<?php
// src/Entity/Video.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="video")
 */
class Video
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /** @ORM\Column(type="string", length=255) */
    private string $filename;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private ?string $originalName = null;

    /** @ORM\Column(type="integer") */
    private int $size;

    /** @ORM\Column(type="string", length=50, nullable=true) */
    private ?string $mimeType = null;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private ?string $thumb = null;

    /** @ORM\Column(type="datetime") */
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    // getters & setters below (omitted for brevity in this doc, add them in your code)
}
```

Générez la migration :

```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

---

## 4) FormType et validation

```php
<?php
// src/Form/VideoType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('videoFile', FileType::class, [
                'label' => 'Fichier vidéo (mp4, webm, mov)',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new Assert\File([
                        'maxSize' => '500M',
                        'mimeTypes' => [
                            'video/mp4','video/x-msvideo','video/quicktime','video/webm','video/*'
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une vidéo valide',
                    ])
                ],
            ]);

            ->add('video', FileType::class, [
                'label' => 'Vidéo (mp4, max 10MB)',
                'mapped' => false,
                'required' => false,
                'maxSize' => '10M',
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => [
                            'video/mp4',
                            'video/webm',
                            'video/ogg',
                            'video/*',
                            'video/x-msvideo',
                            'video/quicktime',
                            'video/webm'
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une vidéo valide (mp4, webm, ogg)',
                    ])
                ],
            ])

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
```

---

## 5) Service `VideoManager`

Ce service s'occupe :

* du déplacement du fichier uploadé vers `public/uploads/videos`
* de générer une vignette via ffmpeg
* (optionnel) de transcodage dans un format standard (mp4 H.264)

```php
<?php
// src/Service/VideoManager.php
namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Process\Process;

class VideoManager
{
    private string $videosDir;
    private string $thumbsDir;
    private LoggerInterface $logger;

    public function __construct(ParameterBagInterface $params, LoggerInterface $logger)
    {
        $this->videosDir = $params->get('videos_directory');
        $this->thumbsDir = $params->get('videos_thumbs_directory');
        $this->logger = $logger;
    }

    public function saveUploadedVideo(UploadedFile $file): array
    {
        $original = $file->getClientOriginalName();
        $ext = $file->guessExtension() ?: $file->getClientOriginalExtension();
        $safeName = bin2hex(random_bytes(8)) . '.' . $ext;

        $file->move($this->videosDir, $safeName);

        // generate thumbnail (at 3s)
        $thumbName = $this->generateThumbnail($safeName, 3);

        return [
            'filename' => $safeName,
            'original' => $original,
            'size' => $file->getSize(),
            'mime' => $file->getClientMimeType(),
            'thumb' => $thumbName,
        ];
    }

    private function generateThumbnail(string $filename, int $second = 1): ?string
    {
        $input = escapeshellarg($this->videosDir . DIRECTORY_SEPARATOR . $filename);
        $thumbFilename = pathinfo($filename, PATHINFO_FILENAME) . '.jpg';
        $output = escapeshellarg($this->thumbsDir . DIRECTORY_SEPARATOR . $thumbFilename);

        // ffmpeg -ss <second> -i input -frames:v 1 -q:v 2 output
        $cmd = ['ffmpeg', '-ss', (string)$second, '-i', $this->videosDir . DIRECTORY_SEPARATOR . $filename, '-frames:v', '1', '-q:v', '2', $this->thumbsDir . DIRECTORY_SEPARATOR . $thumbFilename];

        $process = new Process($cmd);
        $process->setTimeout(30);

        try {
            $process->mustRun();
            return $thumbFilename;
        } catch (\Exception $e) {
            $this->logger->error('Thumbnail generation failed: ' . $e->getMessage());
            return null;
        }
    }

    // Optional: transcode method (left as exercise or implement similarly via ffmpeg)
}
```

N'oubliez pas d'enregistrer le service si nécessaire (autowiring suffit généralement).

---

## 6) Controller (upload, list, show)

```php
<?php
// src/Controller/VideoController.php
namespace App\Controller;

use App\Entity\Video;
use App\Form\VideoType;
use App\Service\VideoManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VideoController extends AbstractController
{
    private VideoManager $videoManager;
    private EntityManagerInterface $em;

    public function __construct(VideoManager $videoManager, EntityManagerInterface $em)
    {
        $this->videoManager = $videoManager;
        $this->em = $em;
    }

    /**
     * @Route("/videos/upload", name="videos_upload")
     */
    public function upload(Request $request): Response
    {
        $form = $this->createForm(VideoType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $form->get('videoFile')->getData();

            $meta = $this->videoManager->saveUploadedVideo($file);

            $video = new Video();
            $video->setFilename($meta['filename']);
            $video->setOriginalName($meta['original']);
            $video->setSize($meta['size']);
            $video->setMimeType($meta['mime']);
            $video->setThumb($meta['thumb']);

            $this->em->persist($video);
            $this->em->flush();

            $this->addFlash('success', 'Vidéo uploadée avec succès');
            return $this->redirectToRoute('videos_list');
        }

        return $this->render('videos/upload.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /** @Route("/videos", name="videos_list") */
    public function list(): Response
    {
        $videos = $this->em->getRepository(Video::class)->findBy([], ['createdAt' => 'DESC']);
        return $this->render('videos/list.html.twig', ['videos' => $videos]);
    }

    /** @Route("/videos/{id}", name="videos_show") */
    public function show(Video $video): Response
    {
        return $this->render('videos/show.html.twig', ['video' => $video]);
    }
}
```

---

## 7) Twig templates

`templates/videos/upload.html.twig`

```twig
{% extends 'base.html.twig' %}
{% block body %}
    <h1>Upload vidéo</h1>
    {{ form_start(form, {'attr': {'enctype': 'multipart/form-data', 'id':'videoUploadForm'}}) }}
    {{ form_row(form.videoFile) }}
    <button class="btn">Uploader</button>
    {{ form_end(form) }}

    <div id="progressContainer" style="display:none">
        <progress id="uploadProgress" value="0" max="100"></progress>
    </div>

    <script>
    // simple JS to show progress using XHR
    const form = document.getElementById('videoUploadForm');
    form.addEventListener('submit', function(e){
        const fileInput = document.querySelector('input[type=file]');
        if(!fileInput.files.length) return;
        e.preventDefault();

        const data = new FormData();
        data.append(fileInput.name, fileInput.files[0]);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '{{ path('videos_upload') }}');
        xhr.upload.addEventListener('progress', function(ev){
            if(ev.lengthComputable){
                document.getElementById('progressContainer').style.display = 'block';
                const percent = Math.round((ev.loaded/ev.total)*100);
                document.getElementById('uploadProgress').value = percent;
            }
        });

        xhr.onload = function(){
            if(xhr.status >= 200 && xhr.status < 300){
                window.location.href = '{{ path('videos_list') }}';
            } else {
                alert('Erreur lors de l\'upload');
            }
        };
        xhr.send(data);
    });
    </script>
{% endblock %}
```

`templates/videos/list.html.twig`

```twig
{% extends 'base.html.twig' %}
{% block body %}
    <h1>Vidéos</h1>
    <a href="{{ path('videos_upload') }}">Uploader une vidéo</a>
    <div class="gallery">
        {% for v in videos %}
            <div class="video-item">
                <a href="{{ path('videos_show', {'id': v.id}) }}">
                    <img src="/uploads/videos/thumbs/{{ v.thumb }}" alt="thumb">
                    <div>{{ v.originalName }}</div>
                </a>
            </div>
        {% else %}
            <p>Aucune vidéo.</p>
        {% endfor %}
    </div>
{% endblock %}
```

`templates/videos/show.html.twig`

```twig
{% extends 'base.html.twig' %}
{% block body %}
    <h1>{{ video.originalName }}</h1>
    <video controls width="800">
        <source src="/uploads/videos/{{ video.filename }}" type="{{ video.mimeType }}">
        Votre navigateur ne supporte pas la balise vidéo.
    </video>
{% endblock %}
```

---

## 8) Sécurité & bonnes pratiques

* Limiter la taille d'upload côté serveur (php.ini `upload_max_filesize`, `post_max_size`) et côté form.
* Scanner / vérifier les fichiers si l'application a des utilisateurs non-trustés.
* Stocker préférablement hors du `public/` et servir via un contrôleur sécurisé si nécessaire.
* Penser au streaming (Range requests) pour les vidéos longues/poids-lourds (implémenter Accept-Ranges support).
* Si beaucoup d'uploads, envisager stockage sur S3 / CDN et génération de vignettes via worker (RabbitMQ / Messenger).

---

## 9) Améliorations possibles

* Upload en chunks / resumable (e.g. tus.io or custom chunking)
* Transcodage asynchrone via queue : push job dans Messenger, worker appelle ffmpeg et met à jour l'entité.
* Génération de plusieurs résolutions (1080p, 720p, 480p) pour adaptative streaming (HLS/DASH).
* Générer manifest HLS via ffmpeg et stocker segments sur CDN.

---

## 10) Récapitulatif rapide des fichiers à créer

* `src/Entity/Video.php`
* `src/Form/VideoType.php`
* `src/Controller/VideoController.php`
* `src/Service/VideoManager.php`
* `templates/videos/upload.html.twig`
* `templates/videos/list.html.twig`
* `templates/videos/show.html.twig`
* paramètres dans `config/services.yaml`

---

Si tu veux, je peux :

* générer les getters/setters complets pour l'entité,
* ajouter le code de migration complet,
* transformer la génération de vignettes en job asynchrone (Messenger + worker),
* implémenter transcodage HLS complet.

Dis-moi ce que tu veux que je fasse ensuite (ou ouvre ce document pour copier/adapter le code).









Dans ton php.ini (ou via .user.ini ou .htaccess si tu es sur un hébergeur mutualisé) :

; Taille max d’un fichier envoyé
upload_max_filesize = 512M
; Taille max totale de la requête POST (doit être >= upload_max_filesize)
post_max_size = 512M
; Durée max d’exécution du script (important pour les grosses vidéos)
max_execution_time = 300
; Durée max de lecture des données d’entrée
max_input_time = 300
; Mémoire max pour PHP
memory_limit = 1G

> 💡 Après modification, redémarre PHP-FPM ou Apache pour que ça prenne effet.



Merci pour le détail 👌. Là tu touches un point assez précis : quand tu envoies un fichier trop lourd en PHP/Symfony, ce n’est **pas Symfony** qui bloque en premier, mais **PHP**.

👉 Ce qui se passe :

* PHP coupe la requête si `upload_max_filesize` ou `post_max_size` est dépassé.
* Symfony ne reçoit alors **aucun fichier**, donc la validation avec `maxSize` n’est jamais déclenchée.
* Résultat : Symfony renvoie une erreur 422 ou recharge la page.

### ✅ Étapes pour corriger et améliorer ton système

1. **Vérifie la config PHP**
   Dans ton `php.ini` (ou `.user.ini` selon ton hébergement) :

   ```ini
   upload_max_filesize = 20M
   post_max_size = 20M
   ```

   Mets une valeur **au moins égale ou supérieure** à ton `maxSize` dans le form.
   Par exemple, si tu limites à `10M`, alors mets `20M` pour `upload_max_filesize` et `post_max_size`.
   👉 Ça permet à Symfony de recevoir le fichier, et ensuite ta validation prend le relais.

2. **Ajoute une validation manuelle en cas de dépassement**
   Symfony fournit l’erreur `UPLOAD_ERR_INI_SIZE` si PHP a bloqué avant. Tu peux la capturer comme ça :

   ```php
   /** @var UploadedFile|null $video */
   $video = $form->get('video')->getData();

   if ($video && $video->getError() === UPLOAD_ERR_INI_SIZE) {
       $form->get('video')->addError(new FormError('La vidéo dépasse la taille maximale autorisée.'));
   }
   ```

   Ça permet d’avoir un message utilisateur clair, même si PHP a rejeté le fichier.

3. **Astuce UX : ajouter un contrôle côté front**
   Même si le back doit valider, tu peux améliorer l’expérience utilisateur :

   ```html
   <input type="file" name="video" accept="video/mp4,video/webm,video/ogg" 
          onchange="if(this.files[0].size > 10*1024*1024){ 
              alert('Fichier trop gros, max 10MB'); 
              this.value = ''; 
          }">
   ```

   👉 Ça évite à l’utilisateur d’attendre un upload inutile.

4. **Conseil bonus sécurité**

   * Stocke la vidéo **hors `public/`** puis génère un lien pour l’affichage.
   * Vérifie l’extension ET le mimeType.
   * Si besoin, utilise un service de conversion (ex : FFmpeg) pour uniformiser les vidéos.

---

👉 Donc le vrai problème vient de la config PHP qui bloque avant Symfony.
Une fois corrigé, ton `maxSize` Symfony fonctionnera bien et renverra un message propre.

Veux-tu que je te propose un **exemple complet amélioré** (formulaire + controller + gestion des erreurs PHP + front check) pour que tu aies une version prête à l’emploi ?










Très bon réflexe 👌 !
Déplacer le traitement de l’upload dans un **listener** (ou mieux : un **EventSubscriber**) est une bonne pratique, car ça te permet :

* de garder ton contrôleur léger (juste gérer la logique métier),
* d’isoler la logique d’upload,
* et d’éviter de ralentir ton action d’édition.

---

### ⚡ Exemple : UploadListener avec Symfony

#### 1. Ton formulaire (inchangé)

```php
->add('video', FileType::class, [
    'label' => 'Vidéo (mp4, max 10MB)',
    'mapped' => false,
    'required' => false,
    'constraints' => [
        new File([
            'maxSize' => '10M',
            'mimeTypes' => [
                'video/mp4',
                'video/webm',
                'video/ogg',
            ],
            'mimeTypesMessage' => 'Veuillez uploader une vidéo valide (mp4, webm, ogg)',
        ])
    ],
])
```

#### 2. Controller (très simple maintenant)

```php
public function edit(Request $request, EntityManagerInterface $em, Post $post): Response
{
    $form = $this->createForm(PostType::class, $post);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Aucune gestion de fichier ici !
        $em->persist($post);
        $em->flush();

        $this->addFlash('success', 'Article mis à jour');
        return $this->redirectToRoute('post_index');
    }

    return $this->render('post/edit.html.twig', [
        'form' => $form->createView(),
    ]);
}
```

#### 3. Le Listener (vrai traitement d’upload)

```php
namespace App\EventListener;

use App\Entity\Post;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;

class VideoUploadListener
{
    private string $uploadDir;

    public function __construct(KernelInterface $kernel)
    {
        $this->uploadDir = $kernel->getProjectDir() . '/public/uploads/videos';
    }

    public function prePersist(Post $post, LifecycleEventArgs $args): void
    {
        $this->handleUpload($post);
    }

    public function preUpdate(Post $post, LifecycleEventArgs $args): void
    {
        $this->handleUpload($post);
    }

    private function handleUpload(Post $post): void
    {
        /** @var UploadedFile|null $video */
        $video = $post->videoFile ?? null; // récupéré depuis le form (champ unmapped)

        if ($video instanceof UploadedFile) {
            $newFilename = uniqid('', true) . '.' . $video->guessExtension();
            $video->move($this->uploadDir, $newFilename);

            // Stocke uniquement le chemin dans l’entité
            $post->setVideoPath('uploads/videos/' . $newFilename);
        }
    }
}
```

#### 4. Déclaration comme service

Dans `services.yaml` :

```yaml
services:
    App\EventListener\VideoUploadListener:
        tags:
            - { name: doctrine.orm.entity_listener, event: prePersist }
            - { name: doctrine.orm.entity_listener, event: preUpdate }
```

#### 5. Ton entité `Post` (ajoute une propriété temporaire)

```php
class Post
{
    // ton champ persisté
    private ?string $videoPath = null;

    // propriété non mappée (juste pour le formulaire)
    public ?UploadedFile $videoFile = null;

    public function getVideoPath(): ?string
    {
        return $this->videoPath;
    }

    public function setVideoPath(?string $videoPath): self
    {
        $this->videoPath = $videoPath;
        return $this;
    }
}
```

---

### 🚀 Résultat

* Le contrôleur **ne gère plus le fichier**.
* Le formulaire passe juste l’`UploadedFile` à `$post->videoFile`.
* Le listener intercepte `prePersist` / `preUpdate`, déplace la vidéo et met à jour `videoPath`.
* Pas de ralentissement dans ton action d’édition.

---

👉 Veux-tu que je te propose une **version avec EventSubscriber + FileUploader service** (encore plus propre, comme le recommande Symfony), ou tu préfères rester sur le listener direct Doctrine ?









En fait, PHP lit le fichier uploadé **avant que Symfony n’arrive à faire la validation**, et si le fichier dépasse `upload_max_filesize` ou qu’il consomme trop de mémoire, tu tombes sur l’erreur `Allowed memory size exhausted`.

---

### 2️⃣ Ce qu’il faut vérifier dans PHP

Dans ton `php.ini` :

```ini
memory_limit = 512M   ; actuellement à 512Mo
upload_max_filesize = 10M  ; limite maximale d'upload
post_max_size = 10M        ; doit être >= upload_max_filesize
```

* L’erreur dit que PHP essaie d’allouer \~180Mo en plus des 512Mo déjà utilisés → il faut **augmenter `memory_limit`** si tu veux gérer des vidéos plus grosses.
* Mais si tu veux que le formulaire affiche une erreur avant tout crash, tu dois **jouer sur `upload_max_filesize` et `post_max_size`**, pas sur `maxSize` du formulaire.

---

### 3️⃣ Solution pratique pour avoir une erreur dans le formulaire

* Assure-toi que le fichier ne dépasse **jamais** `upload_max_filesize`.
* Dans Symfony, `maxSize` ne remplacera pas `upload_max_filesize` : PHP bloque avant que Symfony puisse valider.
* Exemple dans `php.ini` pour 10 Mo max :

```ini
upload_max_filesize = 10M
post_max_size = 12M
memory_limit = 512M
```

* Dans ton formulaire, tu peux garder `maxSize => 10M` pour une double sécurité (c’est cette validation qui donnera un message à l’utilisateur si le fichier est trop gros **mais inférieur à la limite PHP**).

---

### 4️⃣ Pour les vidéos plus grosses

Si tu veux autoriser de gros fichiers :

* Augmente `memory_limit` (mais ce n’est pas la meilleure pratique pour très gros fichiers)
* Ou, **idéalement**, utilise l’upload “streamé” (ex. avec [Symfony HttpFoundation File Stream](https://symfony.com/doc/current/components/http_foundation.html#file-uploads)) ou un service comme **S3** pour ne pas charger toute la vidéo en mémoire.

---

💡 **Résumé** :
Ton formulaire **ne peut pas attraper** les fichiers trop gros pour PHP. Symfony ne valide qu’après que PHP ait chargé le fichier. Donc l’erreur mémoire se produit **avant que Symfony n’ait une chance d’appliquer `maxSize`**.

---

Si tu veux, je peux te montrer **une méthode pour intercepter ce genre de dépassement de taille et afficher une vraie erreur dans Symfony** sans que PHP plante. Veux que je fasse ça ?








- Système de bagdes + popup modal canvas conffetti pour célébrer les nouveaux badges
- !! de messagerie interne




















Parfait 👌 ! On va ajouter un système de réponses aux commentaires (threaded comments) dans ton code React + API Platform. L'idée est de permettre :

➕ un utilisateur peut répondre à un commentaire

🔁 les réponses sont affichées imbriquées

✅ les réponses sont liées à leur parent via parent dans l’API



---

🧱 1. Symfony / API Platform (si ce n’est pas déjà fait)

Dans l’entité Comment.php, tu dois avoir :

#[ORM\ManyToOne(targetEntity: Comment::class, inversedBy: 'replies')]
#[Groups(['comment:read', 'comment:write'])]
private ?Comment $parent = null;

#[ORM\OneToMany(mappedBy: 'parent', targetEntity: Comment::class)]
#[Groups(['comment:read'])]
private Collection $replies;

Et dans CommentNormalizer ou serialization.yaml, assure-toi que replies et parent sont bien dans les groupes.


---

⚛️ 2. Mise à jour du Frontend React


---

✅ Étape 1 : Ajouter le support de replies dans la structure

Ton API Platform renvoie déjà les replies dans comment.replies.


---

✅ Étape 2 : Modifier Comment.jsx

🔁 A. Ajouter le bouton "Répondre" et formulaire

Dans ton composant Comment :

const Comment = memo(({ comment, onDelete, canEdit, onUpdate, onReply }) => {
    const [state, setState] = useState(VIEW)
    const [replying, setReplying] = useState(false)

    const toggleEdit = useCallback(() => {
        setState(state => state === VIEW ? EDIT : VIEW)
    }, [])

    const onDeleteCallback = useCallback(() => {
        onDelete(comment)
    }, [comment])

    const { loading, load } = useFetch(comment['@id'], 'DELETE', onDeleteCallback)

    const onComment = useCallback((newComment) => {
        onUpdate(newComment, comment)
        toggleEdit()
    }, [comment])

    const handleReply = useCallback((reply) => {
        // Ajouter la réponse dans les replies locales (facultatif : à toi de l'optimiser)
        if (!comment.replies) comment.replies = []
        comment.replies.push(reply)
        setReplying(false)
        onReply(reply, comment)
    }, [comment, onReply])

    return <div className="row post-comment" style={{ marginLeft: comment.parent ? 30 : 0 }}>
        <h4 className="col-3">
            <strong>{comment.author?.username}</strong>
            Commenté le <strong>{new Date(comment.publishedAt).toLocaleString()}</strong>
        </h4>
        <div className="col-9">
            {state === VIEW ?
                <p>{comment.content}</p> :
                <CommentForm comment={comment} onComment={onComment} onCancel={toggleEdit} />
            }

            <div style={{ marginTop: 5 }}>
                {(canEdit && state !== EDIT) && <>
                    <button onClick={load.bind(this, null)} disabled={loading} className="btn btn-danger btn-sm">Supprimer</button>
                    <button onClick={toggleEdit} className="btn btn-primary btn-sm">Éditer</button>
                </>}
                {!comment.parent && <button className="btn btn-link btn-sm" onClick={() => setReplying(!replying)}>
                    {replying ? "Annuler" : "Répondre"}
                </button>}
            </div>

            {replying && (
                <CommentForm
                    post={comment.post?.id || comment.post?.split('/').pop()}
                    parentId={comment.id}
                    onComment={handleReply}
                />
            )}

            {/* Affichage récursif des réponses }
            {comment.replies?.map(reply => (
                <Comment
                    key={reply.id}
                    comment={reply}
                    canEdit={reply.author?.id === comment.author?.id}
                    onDelete={onDelete}
                    onUpdate={onUpdate}
                    onReply={onReply}
                />
            ))}
        </div>
    </div>
})


---

✍️ B. Mettre à jour CommentForm pour gérer parentId

const CommentForm = memo(({ post = null, parentId = null, onComment, comment = null, onCancel = null }) => {
    const ref = useRef(null)
    const onSuccess = useCallback(comment => {
        onComment(comment)
        ref.current.value = ''
    }, [ref, onComment])

    const method = comment ? 'PATCH' : 'POST'
    const url = comment ? comment['@id'] : '/api/comments'
    const { loading, errors, load, clearErrors } = useFetch(url, method, onSuccess)

    const onSubmit = useCallback(e => {
        e.preventDefault()
        const payload = {
            content: ref.current.value,
            property: '/api/properties/' + post
        }
        if (parentId) {
            payload.parent = '/api/comments/' + parentId
        }
        load(payload)
    }, [load, ref, post, parentId])

    useEffect(() => {
        if (comment && comment.content && ref.current) {
            ref.current.value = comment.content
        }
    }, [comment, ref])

    return <form onSubmit={onSubmit}>
        {comment === null && <fieldset><legend>Laisser un commentaire</legend></fieldset>}
        <Field
            name="content"
            ref={ref}
            required
            minLength={4}
            help="Les commentaires non conformes à notre code de conduite seront modérés"
            error={errors['content']}
            onChange={clearErrors.bind(this, 'content')}
        >
            Votre commentaire
        </Field>
        <button disabled={loading} className="btn btn-primary">
            {comment === null ? 'Envoyer' : 'Éditer'}
        </button>
        {onCancel && <button className="btn btn-secondary" onClick={onCancel}>
            Annuler
        </button>}
    </form>
})


---

🧠 C. Dans Comments

Tu dois ajouter la gestion des réponses dans onReply :

const onReply = useCallback((reply, parent) => {
    setComment(prev => prev.map(c =>
        c.id === parent.id
            ? { ...c, replies: [...(c.replies || []), reply] }
            : c
    ))
}, [])

Et passer onReply={onReply} dans chaque <Comment /> :

{comments.map((c) => (
    <Comment
        key={c.id}
        comment={c}
        canEdit={c.author && c.author.id === user}
        onDelete={deleteComment}
        onUpdate={updateComment}
        onReply={onReply}
    />
))}


---

✅ Résultat

✅ Chaque commentaire peut avoir une réponse (1 niveau de profondeur)

✅ Les réponses sont affichées sous leur parent avec indentation

✅ Le formulaire est dynamique et propre

✅ Tout reste dans le même usePaginateFetch



---

Souhaites-tu :

📌 Limiter à 1 ou 2 niveaux max ?

🔽 Ajouter un bouton "voir les réponses" pour ne pas tout charger d’un coup ?

📦 Charger les réponses via une requête séparée (GET /api/comments?parent=/api/comments/12) ?





- !! de signalement de commentaires




Voici **la façon la plus simple et efficace** de calculer une distance entre deux points **latitude / longitude** en **MySQL**, en utilisant la **formule de Haversine**.

---

# ✅ **1. Formule Haversine en MySQL (distance en kilomètres)**

```sql
SELECT 
    (6371 * ACOS(
        COS(RADIANS(:lat1)) 
        * COS(RADIANS(:lat2)) 
        * COS(RADIANS(:lng2) - RADIANS(:lng1)) 
        + SIN(RADIANS(:lat1)) 
        * SIN(RADIANS(:lat2))
    )) AS distance_km;
```

### 🔧 Paramètres :

* `:lat1` = latitude du point A
* `:lng1` = longitude du point A
* `:lat2` = latitude du point B
* `:lng2` = longitude du point B
* **6371 = rayon de la Terre en km**

---

# ✅ **2. Exemple concret**

```sql
SET @lat1 = 5.345;
SET @lng1 = -4.024;
SET @lat2 = 6.123;
SET @lng2 = -3.876;

SELECT 
    (6371 * ACOS(
        COS(RADIANS(@lat1)) 
        * COS(RADIANS(@lat2)) 
        * COS(RADIANS(@lng2) - RADIANS(@lng1))
        + SIN(RADIANS(@lat1)) 
        * SIN(RADIANS(@lat2))
    )) AS distance_km;
```

---

# ✅ **3. Calculer la distance entre un point et toutes les lignes d'une table**

Supposons une table :

```sql
places(id, name, lat, lng)
```

Utilisez :

```sql
SELECT 
    id,
    name,
    (6371 * ACOS(
        COS(RADIANS(:lat)) 
        * COS(RADIANS(lat)) 
        * COS(RADIANS(lng) - RADIANS(:lng))
        + SIN(RADIANS(:lat)) 
        * SIN(RADIANS(lat))
    )) AS distance_km
FROM places
ORDER BY distance_km ASC;
```

---

# ✅ **4. Optimisation : Filtrer un rayon (ex : 10 km)**

```sql
SELECT 
    id,
    name,
    (6371 * ACOS(
        COS(RADIANS(:lat)) 
        * COS(RADIANS(lat)) 
        * COS(RADIANS(lng) - RADIANS(:lng))
        + SIN(RADIANS(:lat)) 
        * SIN(RADIANS(lat))
    )) AS distance_km
FROM places
HAVING distance_km <= 10
ORDER BY distance_km;
```

---

# ❗ **5. Attention : ACOS peut retourner NULL**

Si les valeurs sont trop proches ou arrondies, utilisez une version sécurisée :

```sql
SELECT 
    6371 * ACOS(
        LEAST(
            1,
            COS(RADIANS(:lat1))
            * COS(RADIANS(:lat2))
            * COS(RADIANS(:lng2) - RADIANS(:lng1))
            + SIN(RADIANS(:lat1))
            * SIN(RADIANS(:lat2))
        )
    ) AS distance_km;
```

---

# 🟦 Si tu veux, je peux aussi te générer :

✅ une **fonction MySQL** pour calculer la distance
✅ une **requête Laravel / PHP**
✅ une **requête Node.js**
✅ une version **ST_Distance_Sphere()** sans formule

Souhaites-tu une version intégrée à une fonction MySQL ?






Oui 👍 — Symfony n’a pas un équivalent “officiel” natif unique qui fait exactement la même chose que Laravel Sanctum ou Laravel Passport, mais il existe plusieurs bundles/solutions populaires qui couvrent les mêmes besoins selon ce que tu veux faire (authentification API simple, tokens SPA, OAuth2, JWT, etc.).


---

🔐 1. Pour des APIs avec tokens (type SPA/Mobile/API)

✅ LexikJWTAuthenticationBundle

Le plus utilisé pour implémenter des JWT (JSON Web Tokens) dans Symfony.

✔️ Génère des JWT à l’authentification
✔️ Permet de protéger des routes API avec des tokens
✔️ Facile à intégrer avec API Platform ou normal Symfony

📌 Bon si tu veux un système de tokens similaire à Laravel Sanctum (stateless API + tokens).

Installation rapide :

composer require lexik/jwt-authentication-bundle


---

👨‍💻 2. Pour OAuth2 (pour émettre des tokens, scopes, clients, etc.)

✅ trikoder/oauth2-bundle

Bundle populaire pour ajouter un serveur OAuth2 complet dans Symfony (équivalent à Laravel Passport).

✔️ Supporte OAuth2 standard
✔️ Gère clients, tokens, refresh tokens
✔️ Compatible avec API Platform

✔️ Authentification OAuth2 complète
✔️ Access tokens
✔️ Refresh tokens
✔️ Scopes
✔️ Protéger toutes les routes /api/**
✔️ Un fonctionnement quasi identique à Laravel Passport

Installation :

composer require trikoder/oauth2-bundle


---

🛡️ 3. Pour OAuth avec des providers (Google, GitHub…)

✅ HWIOAuthBundle

Orientation client OAuth2 (connexion via réseaux sociaux / autres providers)

✔️ Se connecter avec Google, Facebook, GitHub, etc.
❌ Pas un serveur OAuth pour API (donc pas comme Passport)

Installation :

composer require hwi/oauth-bundle


---

⭐ Comparaison rapide

Besoin Bundle recommandé

Auth API simple avec tokens LexikJWTAuthenticationBundle
OAuth2 complet (clients, scopes, tokens) trikoder/oauth2-bundle
OAuth pour login via providers externes HWIOAuthBundle
API + Auth avec bcp de features API Platform + JWT



---

🧠 Et Symfony “officiel” ?

Symfony inclut un composant Security très puissant, mais pas de bundle officiel qui gère les tokens/OAuth out-of-the-box comme Laravel Passport/Sanctum.

Tu peux utiliser :

Le système d’authenticator (guard / new authenticator system)

Un provider de token JWT (avec LexikJWTAuthenticationBundle)

Ou OAuth2 (avec trikoder/oauth2-bundle)



---

🧩 Exemples d’usage

📌 Similaire à Laravel Sanctum (tokens API stateless)

→ LexikJWTAuthenticationBundle

📌 Similaire à Laravel Passport (OAuth2)

→ trikoder/oauth2-bundle







Très bonne question — c’est un **point clé des relations ManyToMany dans Doctrine/Symfony**, et il y a souvent confusion autour de `cascade: ['remove']`.

Je vais répondre en **3 parties claires** :

1. **Comment fonctionne un ManyToMany en Symfony (Doctrine)**
2. **Ce qui se passe réellement dans la table intermédiaire**
3. **Faut-il (ou non) utiliser `cascade: ['remove']` ? (réponse courte : NON dans la majorité des cas)**

---

## 1️⃣ Fonctionnement du ManyToMany dans Symfony

Un `ManyToMany` signifie :

* Une entité **A** peut être liée à plusieurs **B**
* Une entité **B** peut être liée à plusieurs **A**
* Doctrine crée une **table de jointure** automatiquement

### Exemple classique : `User` ↔ `Role`

```php
// User.php
#[ORM\Entity]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: 'users')]
    #[ORM\JoinTable(name: 'user_role')]
    private Collection $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }
}
```

```php
// Role.php
#[ORM\Entity]
class Role
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'roles')]
    private Collection $users;
}
```

👉 Doctrine crée automatiquement la table :

```sql
user_role
---------
user_id
role_id
```

---

## 2️⃣ Suppression des relations (table intermédiaire)

### ⚠️ Point très important

👉 **Doctrine supprime automatiquement les lignes de la table intermédiaire** quand :

* Tu enlèves une relation
* OU tu supprimes une entité

### Exemple

```php
$user->getRoles()->removeElement($role);
$entityManager->flush();
```

👉 Doctrine exécute :

```sql
DELETE FROM user_role WHERE user_id = ? AND role_id = ?
```

### Si tu supprimes un User

```php
$entityManager->remove($user);
$entityManager->flush();
```

👉 Doctrine fait :

```sql
DELETE FROM user_role WHERE user_id = ?
DELETE FROM user WHERE id = ?
```

**Sans `cascade: ['remove']`**

---

## 3️⃣ Faut-il utiliser `cascade: ['remove']` ?

### ❌ NON (dans 90% des cas)

```php
#[ORM\ManyToMany(
    targetEntity: Role::class,
    cascade: ['remove'] // ⚠️ dangereux
)]
```

### Pourquoi c’est dangereux ?

Parce que `cascade: ['remove']` signifie :

> « Quand je supprime **User**, supprime aussi les **Role** liés »

👉 Résultat catastrophique :

* Tu supprimes **1 user**
* Doctrine supprime **les rôles**
* Tous les autres users perdent leurs rôles 😱

### Exemple concret du danger

```php
$userA -> Role ADMIN
$userB -> Role ADMIN

remove($userA)
→ ADMIN supprimé
→ userB n’a plus de rôle
```

---

## 4️⃣ Quand `cascade: ['remove']` est acceptable ?

### Cas TRÈS spécifiques seulement

✔️ Quand l’entité liée **n’a aucun sens seule**

Exemples valides :

* `Order` ↔ `TagTemporaire`
* `Article` ↔ `ImageTemporaire`
* `Formulaire` ↔ `ChampDynamique`

Mais même là, **OneToMany** est souvent plus adapté qu’un ManyToMany.

---

## 5️⃣ Bonne pratique recommandée

### ✔️ Ne PAS mettre `cascade: ['remove']` sur un ManyToMany

```php
#[ORM\ManyToMany(targetEntity: Role::class, inversedBy: 'users')]
private Collection $roles;
```

### ✔️ Gérer les relations explicitement

```php
public function addRole(Role $role): self
{
    if (!$this->roles->contains($role)) {
        $this->roles->add($role);
    }

    return $this;
}

public function removeRole(Role $role): self
{
    $this->roles->removeElement($role);
    return $this;
}
```

---

## 6️⃣ Résumé clair

| Question                                                   | Réponse                          |
| ---------------------------------------------------------- | -------------------------------- |
| Doctrine supprime la table intermédiaire automatiquement ? | ✅ OUI                            |
| Faut-il `cascade: ['remove']` pour ça ?                    | ❌ NON                            |
| `cascade: ['remove']` supprime l’entité liée ?             | ✅ OUI                            |
| Dangereux en ManyToMany ?                                  | ⚠️ TRÈS                          |
| Bonne pratique                                             | Gérer les relations manuellement |

---

Si tu veux, je peux aussi te montrer :

* ✔️ **ManyToMany avec entité de jointure**
* ✔️ **Différence ManyToMany vs OneToMany**
* ✔️ **Cas avec `orphanRemoval`**
* ✔️ **Exemples SQL générés par Doctrine**

Dis-moi 👍








Cette erreur est **très courante en Symfony / Doctrine** et elle est **100 % explicable** 👍

---

## ❌ L’erreur

```text
[Syntax Error] line 0, col 18: Error: Expected known function, got 'acos'
```

👉 **Doctrine (DQL)** ne reconnaît **pas** certaines fonctions SQL natives comme `ACOS`, `COS`, `SIN`, etc.

Même si :

* la fonction existe en **MySQL / PostgreSQL**
* la requête fonctionne en SQL pur

➡️ **Doctrine DQL ne les connaît pas par défaut**.

---

## 🧠 Pourquoi ça arrive ?

Doctrine utilise son **propre langage (DQL)**
Il ne comprend **que les fonctions qu’il a enregistrées**.

Exemple typique (calcul de distance GPS) :

```php
->addSelect('ACOS(...)')
```

⛔ Doctrine bloque ici.

---

## ✅ SOLUTIONS (du + propre au + rapide)

---

## 🥇 Solution 1 — Déclarer `ACOS` comme fonction DQL (RECOMMANDÉE)

### 1️⃣ Installer DoctrineExtensions

```bash
composer require beberlei/doctrineextensions
```

---

### 2️⃣ Déclarer la fonction dans Symfony

#### `config/packages/doctrine.yaml`

```yaml
doctrine:
    orm:
        dql:
            numeric_functions:
                acos: DoctrineExtensions\Query\Mysql\Acos
                cos: DoctrineExtensions\Query\Mysql\Cos
                sin: DoctrineExtensions\Query\Mysql\Sin
                radians: DoctrineExtensions\Query\Mysql\Radians
```

⚠️ Pour PostgreSQL :

```yaml
numeric_functions:
    acos: DoctrineExtensions\Query\Postgresql\Acos
```

---

### 3️⃣ Utilisation dans le QueryBuilder

```php
$qb
->addSelect(
    '(6371 * acos(
        cos(radians(:lat)) *
        cos(radians(p.latitude)) *
        cos(radians(p.longitude) - radians(:lng)) +
        sin(radians(:lat)) *
        sin(radians(p.latitude))
    )) AS HIDDEN distance'
)
->setParameter('lat', $lat)
->setParameter('lng', $lng);
```

✅ **Fonctionne parfaitement**

---

## 🥈 Solution 2 — Utiliser une requête SQL native (rapide mais moins clean)

```php
$conn = $entityManager->getConnection();

$sql = "
    SELECT *,
    (6371 * acos(
        cos(radians(:lat)) *
        cos(radians(latitude)) *
        cos(radians(longitude) - radians(:lng)) +
        sin(radians(:lat)) *
        sin(radians(latitude))
    )) AS distance
    FROM property
";

$stmt = $conn->prepare($sql);
$stmt->execute([
    'lat' => $lat,
    'lng' => $lng
]);

$result = $stmt->fetchAllAssociative();
```

✔️ Aucun problème avec `ACOS`
❌ Pas d’Entity Doctrine

---

## 🥉 Solution 3 — Calculer la distance en PHP (petits volumes)

```php
function distanceKm($lat1, $lng1, $lat2, $lng2): float {
    return 6371 * acos(
        cos(deg2rad($lat1)) *
        cos(deg2rad($lat2)) *
        cos(deg2rad($lng2) - deg2rad($lng1)) +
        sin(deg2rad($lat1)) *
        sin(deg2rad($lat2))
    );
}
```

❌ Lent si beaucoup de données
❌ Pas triable en DB

---

## 🚨 Erreur fréquente associée

> `Expected known function, got 'radians'`

➡️ **Même solution** : déclarer `radians`, `cos`, `sin`, etc.

---

## 🧠 Résumé clair

| Cause                          | Solution             |
| ------------------------------ | -------------------- |
| Doctrine ne connaît pas `ACOS` | Déclarer la fonction |
| Fonction SQL native            | DoctrineExtensions   |
| Rapide sans Doctrine           | SQL natif            |
| Petit dataset                  | Calcul PHP           |

---

Si tu veux, dis-moi :

* ✅ **MySQL ou PostgreSQL ?**
* ✅ **Symfony version ?**
* ✅ **Ton use case (distance, recherche géo, tri)**

Je te ferai une **implémentation clé en main adaptée à ton projet** 🔥










Dans Symfony, faire un <select> basé sur un enum dépend surtout de ta version de PHP et de Symfony. Voici les cas propres et recommandés, du plus moderne au plus classique.


---

✅ Cas recommandé (PHP 8.1+ avec Enum natif)

1️⃣ Créer l’Enum

// src/Enum/UserStatus.php
namespace App\Enum;

enum UserStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case BANNED = 'banned';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Actif',
            self::INACTIVE => 'Inactif',
            self::BANNED => 'Banni',
        };
    }
}


---

2️⃣ Utiliser l’Enum dans le FormType (Symfony 5.4+ / 6+)

Symfony fournit EnumType 🎯

// src/Form/UserType.php
namespace App\Form;

use App\Enum\UserStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status', EnumType::class, [
                'class' => UserStatus::class,
                'choice_label' => fn (UserStatus $status) => $status->label(),
                'placeholder' => 'Choisir un statut',
            ]);
    }
}

➡️ Résultat HTML

<select>
  <option value="">Choisir un statut</option>
  <option value="active">Actif</option>
  <option value="inactive">Inactif</option>
  <option value="banned">Banni</option>
</select>


---

3️⃣ Entité Doctrine avec Enum

// src/Entity/User.php
use App\Enum\UserStatus;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Column(enumType: UserStatus::class)]
private ?UserStatus $status = null;

Doctrine gère l’Enum automatiquement 👍


---

✅ Personnaliser l’ordre ou filtrer les valeurs

->add('status', EnumType::class, [
    'class' => UserStatus::class,
    'choices' => [
        UserStatus::ACTIVE,
        UserStatus::INACTIVE,
    ],
    'choice_label' => fn (UserStatus $status) => $status->label(),
])


---

⚠️ Cas PHP < 8.1 (Enum simulé)

Si tu n’as pas les enums natifs, tu peux utiliser une classe constante :

class UserStatus
{
    public const ACTIVE = 'active';
    public const INACTIVE = 'inactive';
    public const BANNED = 'banned';

    public static function choices(): array
    {
        return [
            'Actif' => self::ACTIVE,
            'Inactif' => self::INACTIVE,
            'Banni' => self::BANNED,
        ];
    }
}

FormType :

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

->add('status', ChoiceType::class, [
    'choices' => UserStatus::choices(),
    'placeholder' => 'Choisir un statut',
])


---

🧠 Bonnes pratiques Symfony

✔ Toujours séparer la logique d’affichage (label())
✔ Ne pas mettre les labels dans le FormType
✔ Utiliser EnumType dès que possible
✔ Enum = logique métier, pas juste une liste


---

🚀 Tu veux aller plus loin ?

Je peux te montrer :

Enum + traductions (Translator)

Enum + API Platform

Enum + Filtrage Doctrine

Enum + Admin (EasyAdmin)


Dis-moi 👍







Très bon choix 👍
Remplacer une entité Category par un enum est souvent plus simple, plus performant et plus cohérent quand la liste est fermée (ex : Maison, Appartement, Terrain…).

Je te montre l’architecture propre recommandée en Symfony + Doctrine.


---

🎯 Objectif

Au lieu de :

Property → ManyToOne → Category

Tu veux :

Property → category (enum)


---

✅ 1️⃣ Créer l’Enum PropertyCategory

👉 PHP 8.1+

// src/Enum/PropertyCategory.php
namespace App\Enum;

enum PropertyCategory: string
{
    case HOUSE = 'house';
    case APARTMENT = 'apartment';
    case LAND = 'land';
    case COMMERCIAL = 'commercial';

    public function label(): string
    {
        return match ($this) {
            self::HOUSE => 'Maison',
            self::APARTMENT => 'Appartement',
            self::LAND => 'Terrain',
            self::COMMERCIAL => 'Local commercial',
        };
    }
}


---

✅ 2️⃣ Champ à avoir dans l’entité Property

👉 Un seul champ category de type Enum

// src/Entity/Property.php
namespace App\Entity;

use App\Enum\PropertyCategory;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Property
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(enumType: PropertyCategory::class)]
    private ?PropertyCategory $category = null;

    // getters & setters
    public function getCategory(): ?PropertyCategory
    {
        return $this->category;
    }

    public function setCategory(PropertyCategory $category): self
    {
        $this->category = $category;
        return $this;
    }
}


---

🧠 Que stocke Doctrine en base ?

👉 Une simple colonne VARCHAR

category VARCHAR(255) NOT NULL

Avec comme valeurs :

house
apartment
land
commercial


---

✅ 3️⃣ Formulaire PropertyType

// src/Form/PropertyType.php
namespace App\Form;

use App\Entity\Property;
use App\Enum\PropertyCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;

class PropertyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', EnumType::class, [
                'class' => PropertyCategory::class,
                'choice_label' => fn (PropertyCategory $category) => $category->label(),
                'placeholder' => 'Choisir une catégorie',
            ]);
    }
}


---

✅ 4️⃣ Affichage dans Twig

{{ property.category.label }}

Ou si nullable :

{{ property.category ? property.category.label : '' }}


---

❌ Ce que tu ne dois PLUS avoir

❌ Entité Category
❌ Table category
❌ Relation ManyToOne
❌ Jointures inutiles


---

📌 Quand utiliser un Enum plutôt qu’une entité ?

Cas Enum Entité

Liste fermée ✅ ❌
Peu de changements ✅ ❌
Traduit simplement ✅ ❌
Admin modifiable ❌ ✅
Relations complexes ❌ ✅


👉 Catégorie de Property = ENUM parfait


---

🚀 Bonus : Enum + Traduction Symfony

Si tu veux multi-langue :

public function label(): string
{
    return 'property.category.' . $this->value;
}

Twig :

{{ property.category.label|trans }}


---

🔥 Résumé ultra court

✔ Un seul champ category
✔ Type : PropertyCategory
✔ Doctrine : enumType
✔ Form : EnumType
✔ Plus rapide, plus propre, plus maintenable


---

Si tu veux :

🔍 Filtrer par catégorie

📦 API Platform

🧩 EasyAdmin

🗄️ Migration SQL exacte


Dis-moi 😉






Parfait 👌 On garde ton **SoftDelete simple** (champ `deletedAt`) et on ajoute une **suppression automatique après 30 jours**, **sans filtre Doctrine**.

On va faire ça proprement avec une **commande Symfony (cron job)**.

---

# 🧠 Principe

Un post est :

* 🟢 Actif → `deletedAt = NULL`
* 🗑️ Corbeille → `deletedAt` rempli
* 💀 Supprimé définitivement → si `deletedAt` date de +30 jours

---

# 1️⃣ Ajouter une méthode dans le Repository

**src/Repository/PostRepository.php**

```php
public function findSoftDeletedOlderThan(\DateTimeInterface $date): array
{
    return $this->createQueryBuilder('p')
        ->andWhere('p.deletedAt IS NOT NULL')
        ->andWhere('p.deletedAt <= :limit')
        ->setParameter('limit', $date)
        ->getQuery()
        ->getResult();
}
```

---

# 2️⃣ Créer une commande Symfony

```bash
php bin/console make:command app:purge-deleted-posts
```

---

## ✍️ Code de la commande

**src/Command/PurgeDeletedPostsCommand.php**

```php
namespace App\Command;

use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:purge-deleted-posts',
    description: 'Supprime définitivement les posts soft deleted depuis plus de 30 jours',
)]
class PurgeDeletedPostsCommand extends Command
{
    public function __construct(
        private PostRepository $postRepository,
        private EntityManagerInterface $em
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $limitDate = new \DateTime('-30 days');

        $postsToDelete = $this->postRepository->findSoftDeletedOlderThan($limitDate);

        foreach ($postsToDelete as $post) {
            $this->em->remove($post);
        }

        $this->em->flush();

        $output->writeln(count($postsToDelete) . ' post(s) supprimé(s) définitivement.');

        return Command::SUCCESS;
    }
}
```

---

# 3️⃣ Tester manuellement

```bash
php bin/console app:purge-deleted-posts
```

---

# 4️⃣ Automatiser avec CRON (production)

Sur ton serveur :

```bash
crontab -e
```

Ajouter :

```bash
0 3 * * * /usr/bin/php /chemin/vers/ton/projet/bin/console app:purge-deleted-posts >> /dev/null 2>&1
```

⏰ Ça lance la purge tous les jours à 03h du matin.

---

# 🧩 Bonus : Message clair dans l’admin

Dans ta vue corbeille :

> 🗑️ Les articles dans la corbeille sont supprimés définitivement après **30 jours**.

---

# ✅ Résultat

| Temps depuis suppression | État                     |
| ------------------------ | ------------------------ |
| 0 – 30 jours             | Dans la corbeille        |
| +30 jours                | Supprimé automatiquement |
| Avant 30 jours           | Peut être restauré       |

---

Si tu veux, prochaine amélioration possible :

* 📧 Email d’avertissement avant suppression
* 🔄 Durée configurable (30 → variable en .env)
* 📦 Purge pour toutes les entités soft delete

Tu veux rendre la durée **configurable** plutôt que fixée à 30 jours ?











- Permettre à l'utilisateur connecté de renvoyer un email de vérification tant que son email n’est pas vérifié









Très bonne question 👍
En Symfony, **utiliser un certificat auto-signé en local** est très courant, surtout pour tester :

* HTTPS
* Cookies `Secure`
* OAuth / API externes
* WebRTC / géolocalisation
* Service workers

Je vais te montrer **la méthode propre et recommandée**, puis les alternatives.

---

# ✅ Méthode recommandée (Symfony CLI + certificat local)

👉 **La plus simple et la plus fiable**

## 1️⃣ Installer Symfony CLI

### Linux / macOS

```bash
curl -sS https://get.symfony.com/cli/installer | bash
```

### Windows

👉 Télécharge l’installateur officiel
[https://symfony.com/download](https://symfony.com/download)

Vérifie :

```bash
symfony -v
```

---

## 2️⃣ Installer l’autorité de certification locale

```bash
symfony server:ca:install
```

🔐 Cette commande :

* Crée une **CA locale**
* L’ajoute aux certificats de confiance de l’OS
* Permet des certificats HTTPS **sans alerte navigateur**

---

## 3️⃣ Démarrer ton projet Symfony en HTTPS

Dans ton projet :

```bash
symfony serve
```

Ou explicitement :

```bash
symfony serve --https
```

Symfony va générer automatiquement un certificat valide pour :

```
https://127.0.0.1
https://localhost
https://<nom-du-projet>.localhost
```

👉 Tu auras **le cadenas vert** 🔒

---

## 4️⃣ Accéder à ton site

```text
https://localhost:8000
```

ou

```text
https://nom-du-projet.localhost
```

---

## 🧠 Avantages de cette méthode

✔️ Aucun warning navigateur
✔️ Compatible Chrome / Firefox / Edge
✔️ Fonctionne avec OAuth, cookies Secure
✔️ Zéro configuration Symfony
✔️ Idéal pour dev local

---

# 🔁 Alternative 1 : Certificat auto-signé OpenSSL (manuel)

⚠️ Plus technique, moins confortable

## 1️⃣ Générer le certificat

```bash
mkdir certs && cd certs
openssl req -x509 -nodes -days 365 \
  -newkey rsa:2048 \
  -keyout localhost.key \
  -out localhost.crt
```

Réponds :

```
Common Name (CN) = localhost
```

---

## 2️⃣ Configurer Symfony (Apache ou Nginx)

### Exemple Nginx

```nginx
server {
    listen 443 ssl;
    server_name localhost;

    ssl_certificate     /path/certs/localhost.crt;
    ssl_certificate_key /path/certs/localhost.key;

    root /var/www/symfony/public;
    index index.php;
}
```

---

## 3️⃣ Ajouter le certificat à ton OS (optionnel)

Sans ça :

* Chrome affichera ⚠️
* Certaines APIs ne fonctionneront pas

👉 Importer `localhost.crt` dans :

* **Autorités de certification de confiance**

---

## ❌ Inconvénients

* Alerte navigateur
* OAuth souvent refusé
* Plus de config serveur

---

# 🔁 Alternative 2 : mkcert (très populaire)

👉 Comparable à Symfony CLI, mais générique

## 1️⃣ Installer mkcert

```bash
mkcert -install
```

## 2️⃣ Générer le certificat

```bash
mkcert localhost 127.0.0.1 ::1
```

## 3️⃣ Utiliser dans ton serveur web

```nginx
ssl_certificate localhost.pem;
ssl_certificate_key localhost-key.pem;
```

✔️ Certificat reconnu par le navigateur
✔️ Fonctionne partout

---

# 🧩 Symfony + HTTPS : points importants

### Cookies Secure

```yaml
# config/packages/framework.yaml
framework:
  session:
    cookie_secure: auto
```

---

### URLs générées en HTTPS

```yaml
framework:
  trusted_proxies: '127.0.0.1'
```

---

### Forcer HTTPS en dev (optionnel)

```yaml
# config/packages/dev/framework.yaml
framework:
  router:
    strict_requirements: true
```

---

# 🟢 Recommandation finale (claire)

| Cas              | Solution             |
| ---------------- | -------------------- |
| Symfony en local | ✅ **Symfony CLI**    |
| Autres projets   | mkcert               |
| Serveur réel     | Let’s Encrypt        |
| Tests OAuth      | Symfony CLI / mkcert |

---

Si tu veux, dis-moi :

* **Ton OS** (Windows / Linux / macOS)
* **Ton serveur local** (Symfony CLI, Apache, Nginx, Docker)

Je t’expliquerai la config **exacte adaptée à ton setup** 🔧🔐







Très bonne question — c’est un **classique avec Symfony + LiipImagineBundle** 🙂
La différence vient du **serveur utilisé** et de la façon dont Symfony intercepte les requêtes.

---

## 1. Pourquoi ça marche avec `symfony serve`

Quand tu fais :

```bash
symfony serve
```

👉 Symfony CLI lance un **serveur HTTP “intelligent”** qui :

* Reproduit le comportement de **Apache / Nginx**
* Redirige **toutes les requêtes** vers `public/index.php`
* Gère correctement :

  * les routes Symfony
  * les fichiers virtuels (comme ceux générés par LiipImagine)
  * les headers, le cache, les rewrites

### LiipImagineBundle dépend fortement de ça

LiipImagine :

* intercepte des URLs comme :

  ```
  /media/cache/resolve/thumb/images/photo.jpg
  ```
* cette URL **n’est pas un vrai fichier au départ**
* elle doit passer par Symfony (route `liip_imagine_filter`)
* Symfony génère l’image et la met en cache

👉 Avec `symfony serve`, **la requête passe bien par Symfony**, donc tout fonctionne.

---

## 2. Pourquoi ça ne marche PAS avec `php -S`

Quand tu fais :

```bash
php -S localhost:8000 -t public
```

⚠️ Tu utilises le **serveur PHP interne**, qui a de grosses limitations :

### Problème principal

* Le serveur PHP intégré :

  * **ne gère pas les règles de réécriture (rewrite)**
  * sert les fichiers **tels quels**
  * ne redirige PAS automatiquement vers `index.php`

Donc quand LiipImagine appelle :

```
/media/cache/resolve/thumb/images/photo.jpg
```

PHP fait :

> "Ce fichier n’existe pas physiquement ? → 404 ❌"

Symfony **n’est jamais appelé**, donc :

* la route LiipImagine n’est pas exécutée
* l’image n’est jamais générée

---

## 3. Preuve rapide (test)

Si tu fais :

```bash
php -S localhost:8000 -t public
```

Puis tu ouvres :

```
http://localhost:8000/media/cache/resolve/thumb/...
```

👉 Tu verras généralement :

* 404
* ou fichier introuvable
* ou rien du tout

Alors qu’avec `symfony serve` → OK ✅

---

## 4. Solution si tu veux utiliser `php -S` quand même

### Solution 1 (recommandée) : utiliser le router PHP

Crée un fichier `router.php` à la racine :

```php
<?php
// router.php

if (php_sapi_name() === 'cli-server') {
    $path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    $file = __DIR__.'/public'.$path;

    if (is_file($file)) {
        return false; // Sert le fichier statique
    }
}

require __DIR__.'/public/index.php';
```

Puis lance :

```bash
php -S localhost:8000 router.php
```

👉 Cette fois :

* les fichiers existants sont servis
* les routes virtuelles (LiipImagine) passent par Symfony ✅

---

### Solution 2 : utiliser Apache ou Nginx

En environnement réel (ou Docker) :

* Apache avec `.htaccess`
* ou Nginx avec `try_files`

Exemple Nginx :

```nginx
location / {
    try_files $uri /index.php$is_args$args;
}
```

---

## 5. Pourquoi Symfony CLI est préférable en dev

Symfony CLI :

* gère HTTPS automatiquement
* gère les rewrites
* compatible avec tous les bundles (LiipImagine, VichUploader, etc.)
* plus proche d’un vrai serveur de prod

👉 **C’est pour ça que LiipImagine “marche par magie” avec `symfony serve`**

---

## 6. Résumé rapide

| Serveur             | LiipImagine         |
| ------------------- | ------------------- |
| `symfony serve`     | ✅ Fonctionne        |
| `php -S -t public`  | ❌ Ne fonctionne pas |
| `php -S router.php` | ✅ Fonctionne        |
| Apache / Nginx      | ✅ Fonctionne        |

---

Si tu veux, je peux aussi :

* t’expliquer **le cycle exact d’une requête LiipImagine**
* t’aider à **configurer Nginx / Apache**
* ou voir **ton config LiipImagine** pour vérifier qu’elle est optimale

Dis-moi 👍







Avec **Symfony + Turbo (Hotwire)**, le problème vient du fait que **Turbo garde le DOM en cache** lors de la navigation arrière/avant.
Du coup **Select2 est déjà initialisé**, et quand la page revient, tu te retrouves avec :

* un Select2 dupliqué
* ou un Select2 cassé
* ou des événements attachés plusieurs fois

👉 **Il faut détruire Select2 avant que Turbo mette la page en cache**, puis le **réinitialiser après le rendu**.

---

## ✅ Solution recommandée (propre & stable)

### 1️⃣ Détruire Select2 avant la mise en cache Turbo

Turbo déclenche l’événement **`turbo:before-cache`** juste avant de sauvegarder le DOM.

```js
document.addEventListener('turbo:before-cache', () => {
    $('.select2-hidden-accessible').each(function () { // 'select2-hidden-accessible' permet de vérifier si  l'initialisation du plugin Pour vérifier si Select2 a été initialisé sur un élément DOM particulier
        if ($(this).data('select2')) { // vérifier si l’élément courant utilise le plugin Select2, puis à le désactiver (le détruire) si c’est le cas.
            $(this).select2('destroy');
        }
    });
});
```

✔️ Ça évite que Select2 soit stocké dans le cache Turbo.

---

### 2️⃣ Réinitialiser Select2 après chaque navigation Turbo

Utilise **`turbo:load`** (et pas `DOMContentLoaded` ❌).

```js
document.addEventListener('turbo:load', () => {
    $('.js-select2').select2({
        width: '100%',
        theme: 'bootstrap-5'
    });
});
```

HTML :

```html
<select class="js-select2" name="category">
    <option value="1">Cat 1</option>
</select>
```

---

## ✅ Version **idéale avec Stimulus** (fortement conseillée)

Turbo + Select2 **sans Stimulus = galère assurée** 😅

---

### 1️⃣ Controller Stimulus

```js
// assets/controllers/select2_controller.js
import { Controller } from '@hotwired/stimulus';
import $ from 'jquery';
import 'select2';

export default class extends Controller {
    connect() {
        $(this.element).select2({
            width: '100%',
            theme: 'bootstrap-5'
        });
    }

    disconnect() {
        if ($(this.element).data('select2')) {
            $(this.element).select2('destroy');
        }
    }
}
```

---

### 2️⃣ HTML

```html
<select data-controller="select2">
    <option value="1">Cat 1</option>
</select>
```

✔️ Stimulus appelle automatiquement :

* `connect()` → initialisation
* `disconnect()` → destruction (navigation Turbo, cache, etc.)

👉 **Zéro bug de back navigation**

---

## ❌ Ce qu’il ne faut PAS faire

❌ `DOMContentLoaded`
❌ Initialiser Select2 sans destruction
❌ Forcer `data-turbo="false"` sur toute la page
❌ Réinitialiser Select2 sans vérifier s’il existe déjà

---

## 🧠 Bonus : désactiver le cache Turbo sur un élément précis

Si un Select2 est vraiment trop complexe :

```html
<div data-turbo-cache="false">
    <select data-controller="select2">...</select>
</div>
```

---

## 🏁 Conclusion

| Approche                  | Fiabilité |
| ------------------------- | --------- |
| jQuery + turbo events     | ⭐⭐⭐       |
| **Stimulus (recommandé)** | ⭐⭐⭐⭐⭐     |
| data-turbo="false" global | ❌         |

---

Si tu veux, je peux :

* adapter ça à **Symfony UX**
* gérer **Select2 AJAX**
* gérer **Select2 multi / tags**
* te montrer comment faire avec **Turbo Frames**

Dis-moi 👍






Ah, tu veux savoir comment **normaliser des données dans Symfony**, c’est-à-dire transformer des objets PHP en **tableaux ou JSON** (et inversement) pour les API ou DataTables. Je vais t’expliquer clairement et étape par étape. 👌

---

## 1️⃣ Qu’est-ce que la **normalisation** ?

* **Normalisation** = transformer un objet PHP (par ex. ton entité `Marque`) en **tableau associatif**.
* **Dénormalisation** = transformer un **tableau ou JSON** en objet PHP.

Symfony fournit le **Serializer Component** pour ça.

---

## 2️⃣ Installer le Serializer (si ce n’est pas fait)

Si ton projet ne l’a pas encore :

```bash
composer require symfony/serializer
```

Tu peux aussi installer les annotations si tu veux gérer des groupes :

```bash
composer require symfony/serializer-pack
```

---

## 3️⃣ Exemple simple de normalisation

Disons que tu as une entité `Marque` :

```php
namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;

class Marque
{
    #[Groups(['show'])]
    private ?int $id = null;

    #[Groups(['show'])]
    private ?string $libelleFr = null;

    public function getId(): ?int { return $this->id; }
    public function getLibelleFr(): ?string { return $this->libelleFr; }
}
```

Dans ton controller :

```php
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

public function testNormalize(Marque $marque)
{
    $normalizer = new ObjectNormalizer();
    $serializer = new Serializer([$normalizer]);

    $data = $serializer->normalize($marque); // transforme l'objet en tableau

    return $this->json($data); // Symfony transforme le tableau en JSON
}
```

---

## 4️⃣ Avec des **groups** (très utile pour éviter les circular references)

```php
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

$data = $serializer->normalize(
    $marque,
    null,
    [
        'groups' => 'show'
    ]
);
```

✅ Seuls les champs avec `#[Groups(['show'])]` seront inclus dans le tableau.

---

## 5️⃣ Gestion des **relations / circular references**

Si ton entité `Marque` a une relation avec `Produit` et que `Produit` a une relation vers `Marque`, tu peux limiter la profondeur :

```php
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

$data = $serializer->normalize(
    $marque,
    null,
    [
        'groups' => 'show',
        AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true
    ]
);
```

Ou utiliser `#[MaxDepth(1)]` sur la propriété dans l’entité.

---

## 6️⃣ Dénormalisation (tableau → objet)

```php
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

$serializer = new Serializer([new ObjectNormalizer()]);

$marqueArray = [
    'id' => 1,
    'libelleFr' => 'Toyota'
];

$marque = $serializer->denormalize($marqueArray, Marque::class);
```

---

## 7️⃣ Utilisation typique avec **DataTables**

Dans un endpoint serverSide :

```php
$serializer = new Serializer([new ObjectNormalizer()]);

$data = $serializer->normalize($listeMarques, null, ['groups' => 'show:liste']);

return $this->json([
    'draw' => $draw,
    'recordsTotal' => count($listeMarques),
    'recordsFiltered' => count($listeMarques),
    'data' => $data
]);
```

Ici `data` devient un **tableau simple**, parfait pour DataTables.

---

💡 **Astuce :** Toujours limiter les propriétés envoyées (avec `groups` ou `Ignore`) pour éviter les **circular references** et améliorer les performances.

---

Si tu veux, je peux te montrer **un exemple concret complet** de controller Symfony qui renvoie des données normalisées pour un DataTable, **en gérant correctement les relations pour éviter l’erreur circular reference**.

Veux‑tu que je fasse ça ?









Très bonnes questions — ça touche au cœur de la sécurité Symfony 🔐
Je t’explique ça simplement.

---

# 🧩 1️⃣ `role_hierarchy` — À quoi ça sert ?

Le **role hierarchy** permet de dire :
👉 *“Si un utilisateur a ce rôle, alors il a aussi automatiquement les autres”*

C’est un système d’**héritage de rôles**.

### 📌 Exemple

```yaml
# config/packages/security.yaml
security:
    role_hierarchy:
        ROLE_ADMIN: ROLE_USER
        ROLE_SUPER_ADMIN: [ROLE_ADMIN, ROLE_ALLOWED_TO_SWITCH]
```

### 🔍 Ça veut dire quoi ?

| Rôle attribué    | A aussi automatiquement                         |
| ---------------- | ----------------------------------------------- |
| ROLE_USER        | rien de plus                                    |
| ROLE_ADMIN       | ROLE_USER                                       |
| ROLE_SUPER_ADMIN | ROLE_ADMIN + ROLE_USER + ROLE_ALLOWED_TO_SWITCH |

👉 Donc si ton utilisateur a juste `ROLE_ADMIN` en base, Symfony le considère aussi comme `ROLE_USER`.

---

### 🧠 Important à comprendre

Le hierarchy fonctionne :

* ✅ avec `is_granted('ROLE_USER')`
* ✅ avec `@IsGranted`
* ✅ avec `access_control`

Mais ❌ **pas pour enregistrer en base**
Tu stockes seulement le rôle principal (`ROLE_ADMIN`) — Symfony ajoute les autres en mémoire.

---

### 🛡 Exemple concret dans un controller

```php
#[IsGranted('ROLE_USER')]
public function profile() {}
```

Un admin pourra accéder, car il hérite de `ROLE_USER`.

---

# 🔐 2️⃣ `enable_csrf` dans `form_login`

```yaml
firewalls:
    main:
        form_login:
            enable_csrf: true
```

### 📌 CSRF = Cross-Site Request Forgery

C’est une protection contre les attaques où un site malveillant essaie de faire soumettre un formulaire à la place de l’utilisateur (à son insu).

---

### 🧨 Sans CSRF

Un hacker pourrait créer un site avec un formulaire caché qui envoie :

```
POST /login
email=hacker@mail.com
password=123456
```

Et si ton utilisateur est connecté ailleurs, son navigateur pourrait envoyer la requête 😬

---

### 🛡 Avec `enable_csrf: true`

Symfony ajoute un **token secret caché** dans le formulaire :

```html
<input type="hidden" name="_csrf_token" value="abc123xyz">
```

Au moment du login :

* Symfony vérifie que le token est valide
* S’il ne correspond pas → ❌ login refusé

---

### 🧠 Important

Si tu actives `enable_csrf`, tu dois avoir dans ton formulaire Twig :

```twig
<input type="hidden" name="_csrf_token"
       value="{{ csrf_token('authenticate') }}">
```

👉 `'authenticate'` doit correspondre à la valeur par défaut utilisée par `form_login`.

---

## 🎯 Résumé rapide

| Élément          | Rôle                                                    |
| ---------------- | ------------------------------------------------------- |
| `role_hierarchy` | Donne automatiquement des rôles supplémentaires         |
| `enable_csrf`    | Protège le formulaire de login contre les attaques CSRF |
| CSRF token       | Vérifie que la requête vient bien de TON formulaire     |

---

Si tu veux, je peux aussi t’expliquer :
➡️ la différence entre **voters** et **role_hierarchy**
➡️ ou comment faire des permissions fines par action (VIEW, EDIT, DELETE)








Les **erreurs HTTP** (codes de statut) permettent à ton API de communiquer clairement ce qui s’est passé côté serveur.
Dans une **architecture séparée** (ex : frontend React/Vue/Angular + API Symfony/Laravel), elles sont essentielles pour que le frontend sache quoi faire (afficher une erreur, rediriger, relancer une requête, etc.).

---

# 📚 1️⃣ Les grandes familles de codes HTTP

## 🟢 1xx – Information

Rarement utilisées en API moderne.

---

## 🟢 2xx – Succès

| Code                    | Signification      | Quand l’utiliser   |
| ----------------------- | ------------------ | ------------------ |
| **200 OK**              | Succès classique   | GET, PUT           |
| **201 Created**         | Ressource créée    | POST               |
| **204 No Content**      | Succès sans retour | DELETE             |
| **206 Partial Content** | Contenu partiel    | Pagination avancée |

👉 En API REST, 200 / 201 / 204 sont les plus utilisés.

---

## 🟡 3xx – Redirection

Peu utilisées en API JSON.

| Code | Usage                  |
| ---- | ---------------------- |
| 301  | Redirection permanente |
| 302  | Redirection temporaire |

---

## 🔴 4xx – Erreur côté client

Très importantes en architecture séparée.

| Code                         | Signification         | Cas concret         |
| ---------------------------- | --------------------- | ------------------- |
| **400 Bad Request**          | Données invalides     | JSON mal formé      |
| **401 Unauthorized**         | Non authentifié       | Token absent        |
| **403 Forbidden**            | Pas le droit          | Mauvais rôle        |
| **404 Not Found**            | Ressource inexistante | ID incorrect        |
| **405 Method Not Allowed**   | Mauvaise méthode      | POST sur route GET  |
| **409 Conflict**             | Conflit logique       | Email déjà utilisé  |
| **422 Unprocessable Entity** | Validation échouée    | Formulaire invalide |

💡 En API moderne, **422 est très propre pour les erreurs de validation**.

---

## 🔥 5xx – Erreur serveur

| Code                          | Signification |
| ----------------------------- | ------------- |
| **500 Internal Server Error** | Bug backend   |
| **502 Bad Gateway**           | Mauvais proxy |
| **503 Service Unavailable**   | Maintenance   |

👉 500 ne doit jamais exposer l’erreur technique au frontend en production.

---

# 🏗 2️⃣ Comment les utiliser dans une architecture séparée

Prenons un exemple classique :

Frontend (React)
Backend API (Symfony)

---

## 🎯 Cas 1 : Création réussie

POST `/api/users`

Réponse backend :

```json
HTTP 201 Created
{
  "id": 15,
  "email": "test@mail.com"
}
```

Frontend :

```js
if (response.status === 201) {
   // rediriger vers la page profil
}
```

---

## 🎯 Cas 2 : Erreur de validation

Backend :

```json
HTTP 422 Unprocessable Entity
{
  "errors": {
    "email": "Email invalide",
    "password": "Mot de passe trop court"
  }
}
```

Frontend :

```js
if (response.status === 422) {
   setErrors(data.errors)
}
```

---

## 🎯 Cas 3 : Utilisateur non connecté

Backend :

```
HTTP 401 Unauthorized
```

Frontend :

```js
if (response.status === 401) {
   redirect("/login")
}
```

---

## 🎯 Cas 4 : Mauvais rôle

```
HTTP 403 Forbidden
```

Frontend :

```js
Afficher("Vous n'avez pas les permissions nécessaires")
```

---

# 🧱 3️⃣ Bonnes pratiques en architecture séparée

## ✅ 1. Toujours renvoyer du JSON

Même pour les erreurs :

```json
{
  "message": "Accès refusé"
}
```

---

## ✅ 2. Ne jamais exposer les erreurs techniques

❌ Mauvais :

```json
{
  "error": "SQLSTATE[HY000]..."
}
```

✔ Bon :

```json
{
  "message": "Une erreur interne est survenue"
}
```

---

## ✅ 3. Centraliser la gestion des erreurs côté backend

En Symfony par exemple :

* EventListener sur `ExceptionEvent`
* ApiPlatform ErrorNormalizer
* HttpException

---

## ✅ 4. Standardiser ton format d’erreur

Exemple propre :

```json
{
  "status": 422,
  "message": "Validation échouée",
  "errors": {
     "email": "Email invalide"
  }
}
```

---

# 🏛 4️⃣ En Clean Architecture / DDD

Dans une architecture propre :

* Le **Domain** ne connaît pas HTTP
* L’**Application** lève des exceptions métiers
* L’**Infrastructure** les transforme en codes HTTP

Exemple :

```php
throw new UserAlreadyExistsException();
```

Puis dans un ExceptionListener :

```php
return new JsonResponse(
    ['message' => 'Utilisateur déjà existant'],
    409
);
```

👉 Le domaine reste propre, l’API décide du code HTTP.

---

# 🎯 Résumé pratique

En API moderne :

* 200 → succès
* 201 → création
* 204 → suppression
* 400 → requête invalide
* 401 → non connecté
* 403 → interdit
* 404 → introuvable
* 409 → conflit métier
* 422 → validation
* 500 → bug serveur

---

Si tu veux, je peux te faire un exemple concret en Symfony avec :

* un controller
* un ExceptionListener
* une gestion propre multi-entreprise (comme ton cas précédent)

Tu veux un exemple orienté Symfony pur ou ApiPlatform ?






Voici la **liste des principaux codes d’erreurs HTTP (4xx et 5xx)** avec leurs numéros et leur signification 👇

---

# 🔴 4xx — Erreurs côté client

👉 La requête envoyée par le client est incorrecte ou interdite.

| Code    | Nom                             | Signification                                          |
| ------- | ------------------------------- | ------------------------------------------------------ |
| **400** | Bad Request                     | Requête mal formée (JSON invalide, paramètre manquant) |
| **401** | Unauthorized                    | Non authentifié (token absent/invalide)                |
| **402** | Payment Required                | Réservé (paiement requis, rarement utilisé)            |
| **403** | Forbidden                       | Authentifié mais interdit (mauvais rôle)               |
| **404** | Not Found                       | Ressource inexistante                                  |
| **405** | Method Not Allowed              | Mauvaise méthode HTTP                                  |
| **406** | Not Acceptable                  | Format non supporté                                    |
| **407** | Proxy Authentication Required   | Authentification proxy requise                         |
| **408** | Request Timeout                 | Délai dépassé                                          |
| **409** | Conflict                        | Conflit logique (ex : email déjà utilisé)              |
| **410** | Gone                            | Ressource supprimée définitivement                     |
| **411** | Length Required                 | Content-Length requis                                  |
| **412** | Precondition Failed             | Condition non respectée                                |
| **413** | Payload Too Large               | Fichier trop volumineux                                |
| **414** | URI Too Long                    | URL trop longue                                        |
| **415** | Unsupported Media Type          | Mauvais Content-Type                                   |
| **416** | Range Not Satisfiable           | Plage demandée invalide                                |
| **417** | Expectation Failed              | Expect header invalide                                 |
| **418** | I'm a teapot                    | Code humoristique                                      |
| **422** | Unprocessable Entity            | Validation échouée                                     |
| **423** | Locked                          | Ressource verrouillée                                  |
| **424** | Failed Dependency               | Dépendance échouée                                     |
| **425** | Too Early                       | Requête envoyée trop tôt                               |
| **426** | Upgrade Required                | Mise à niveau requise                                  |
| **428** | Precondition Required           | Condition requise                                      |
| **429** | Too Many Requests               | Trop de requêtes (rate limit)                          |
| **431** | Request Header Fields Too Large | Headers trop volumineux                                |
| **451** | Unavailable For Legal Reasons   | Bloqué légalement                                      |

---

# 🔥 5xx — Erreurs côté serveur

👉 Le serveur a échoué à traiter une requête valide.

| Code    | Nom                             | Signification                          |
| ------- | ------------------------------- | -------------------------------------- |
| **500** | Internal Server Error           | Erreur interne serveur                 |
| **501** | Not Implemented                 | Fonction non implémentée               |
| **502** | Bad Gateway                     | Mauvaise réponse d’un serveur en amont |
| **503** | Service Unavailable             | Service indisponible (maintenance)     |
| **504** | Gateway Timeout                 | Timeout serveur amont                  |
| **505** | HTTP Version Not Supported      | Version HTTP non supportée             |
| **506** | Variant Also Negotiates         | Problème négociation                   |
| **507** | Insufficient Storage            | Stockage insuffisant                   |
| **508** | Loop Detected                   | Boucle détectée                        |
| **510** | Not Extended                    | Extension requise                      |
| **511** | Network Authentication Required | Authentification réseau requise        |

---

# 🎯 Les plus utilisés en API REST moderne

En pratique (ex : API Symfony + React) tu utilises surtout :

* **400** → mauvaise requête
* **401** → non connecté
* **403** → interdit
* **404** → introuvable
* **409** → conflit métier
* **422** → validation
* **429** → trop de requêtes
* **500** → bug serveur
* **503** → maintenance

---

Si tu veux, je peux te faire :

* ✅ un tableau PDF téléchargeable
* ✅ un résumé ultra synthétique pour révision
* ✅ une version orientée API Platform
* ✅ ou une fiche spéciale multi-entreprise

Lequel t’intéresse ?






Voici les **codes d’erreurs HTTP les plus importants dans une application Symfony**, avec **quand et comment les utiliser proprement** 👇

---

# 🔴 4xx — Erreurs côté client (les plus utilisées)

| Code                                  | Quand l’utiliser dans Symfony                                      |
| ------------------------------------- | ------------------------------------------------------------------ |
| **400 Bad Request**                   | Requête mal formée (JSON invalide, paramètre obligatoire manquant) |
| **401 Unauthorized**                  | Utilisateur non authentifié (JWT absent/invalide)                  |
| **403 Forbidden**                     | Authentifié mais pas autorisé (Voter refuse l’accès)               |
| **404 Not Found**                     | Entité inexistante (`find()` retourne null)                        |
| **405 Method Not Allowed**            | Mauvaise méthode HTTP (POST sur route GET)                         |
| **409 Conflict**                      | Conflit métier (email déjà utilisé, doublon)                       |
| **415 Unsupported Media Type**        | Mauvais `Content-Type`                                             |
| **422 Unprocessable Entity**          | Erreur de validation (formulaire/DTO invalide)                     |
| **429 Too Many Requests**             | Rate limiting activé                                               |
| **451 Unavailable For Legal Reasons** | Contenu bloqué légalement                                          |

---

# 🔥 5xx — Erreurs serveur

| Code                          | Quand l’utiliser                      |
| ----------------------------- | ------------------------------------- |
| **500 Internal Server Error** | Exception non gérée                   |
| **501 Not Implemented**       | Fonction non implémentée              |
| **502 Bad Gateway**           | Mauvaise réponse d’un service externe |
| **503 Service Unavailable**   | Maintenance ou surcharge              |
| **504 Gateway Timeout**       | Timeout API externe                   |

---

# 🎯 Comment les utiliser proprement en Symfony

## 1️⃣ 404 automatique

```php
$user = $repository->find($id);

if (!$user) {
    throw $this->createNotFoundException('Utilisateur introuvable');
}
```

Symfony renvoie automatiquement :

```
HTTP 404
```

---

## 2️⃣ 403 avec Voter

```php
$this->denyAccessUnlessGranted('EDIT', $user);
```

Si refusé → **403 Forbidden**

---

## 3️⃣ 401 avec Security

Si tu utilises JWT (ex: LexikJWT)
Token absent/invalide → **401 Unauthorized** automatique.

---

## 4️⃣ 422 Validation

Avec Validator :

```php
$errors = $validator->validate($dto);

if (count($errors) > 0) {
    return new JsonResponse([
        'errors' => (string) $errors
    ], 422);
}
```

---

## 5️⃣ 409 Conflit métier

```php
if ($repository->findOneBy(['email' => $email])) {
    throw new ConflictHttpException('Email déjà utilisé');
}
```

Renvoie :

```
HTTP 409
```

---

## 6️⃣ 400 Mauvaise requête

```php
throw new BadRequestHttpException('Paramètre invalide');
```

---

# 🧱 Bonnes pratiques en Symfony API

✔ Toujours renvoyer du JSON
✔ Ne jamais exposer les erreurs techniques en prod
✔ Centraliser les exceptions avec un EventSubscriber
✔ Utiliser les exceptions HTTP natives :

```php
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
```

---

# 🚀 Les codes que tu utiliseras 90% du temps

Dans une vraie API Symfony :

* 200 → OK
* 201 → Created
* 204 → Delete
* 400 → Mauvaise requête
* 401 → Non connecté
* 403 → Interdit
* 404 → Introuvable
* 409 → Conflit
* 422 → Validation
* 500 → Bug

---

Si tu veux, je peux te faire :

* 📦 Un exemple complet controller + service + exception propre
* 🔐 Gestion multi-entreprise avec 403 sécurisé
* 🧠 Une architecture Clean Symfony avec mapping automatique des exceptions

Lequel t’aiderait le plus pour ton projet actuel ?



    404 - Not Found : La route demandée n'existe pas
    405 - Methode Not Allowed : La méthode HTTP utilisé n'est pas autorisée pour cette action
    500 - Internal Server Error : Une erreur interne se produit
    401 - Unauthorized : L'utiliateur n'est pas authentifié ou n'a pas les permissions nécessaires
    403 - Forbidden : Accès refusé à une ressource protégée
    400 - Bad Request : La requête envoyée par le client est incorrect





Voici une liste structurée des **types d’applications** courantes avec leurs **modules typiques** 👇

---

## 1️⃣ Application **E-commerce**

Exemples : Amazon, Jumia

**Modules :**

* Gestion des utilisateurs (inscription, login)
* Catalogue produits
* Catégories
* Panier
* Commandes
* Paiement (intégration Stripe, PayPal, etc.)
* Gestion des livraisons
* Gestion des stocks
* Promotions / Coupons
* Avis & commentaires
* Tableau de bord administrateur
* Reporting & statistiques

---

## 2️⃣ Application **de gestion d’entreprise (ERP)**

Exemple : SAP ERP

**Modules :**

* Comptabilité
* Facturation
* Gestion des clients (CRM)
* Gestion fournisseurs
* Gestion des stocks
* Ressources humaines
* Gestion des projets
* Gestion multi-entreprise
* Reporting financier
* Gestion des rôles et permissions

---

## 3️⃣ Application **bancaire / fintech**

Exemples : PayPal, Wave

**Modules :**

* Création de compte
* Vérification KYC
* Portefeuille (wallet)
* Dépôt / Retrait
* Transfert d’argent
* Historique des transactions
* Notifications
* Sécurité (2FA)
* Support client

---

## 4️⃣ Application **réseau social**

Exemples : Facebook, Instagram

**Modules :**

* Profil utilisateur
* Publication (posts)
* Likes / commentaires
* Messagerie privée
* Amis / abonnements
* Notifications
* Stories
* Modération
* Paramètres de confidentialité

---

## 5️⃣ Application **SaaS (Software as a Service)**

Exemples : Dropbox, Notion

**Modules :**

* Authentification
* Gestion abonnement (plans, facturation)
* Dashboard utilisateur
* Gestion des rôles
* Paramètres
* API publique
* Logs & monitoring
* Multi-tenant (multi-entreprise)

---

## 6️⃣ Application **éducative (E-learning)**

Exemple : Coursera

**Modules :**

* Inscription étudiant
* Catalogue des cours
* Vidéos / contenus
* Quiz & examens
* Suivi de progression
* Certificats
* Paiement
* Forum / discussion
* Administration formateurs

---

## 7️⃣ Application **de gestion hospitalière**

**Modules :**

* Gestion des patients
* Dossiers médicaux
* Prise de rendez-vous
* Facturation
* Gestion du personnel
* Gestion pharmacie
* Gestion laboratoire
* Rapports médicaux

---

## 8️⃣ Application **de gestion immobilière**

**Modules :**

* Gestion des biens
* Gestion locataires
* Contrats
* Paiement loyers
* Maintenance
* Comptabilité
* Rapports

---

## 9️⃣ Application **logistique / transport**

Exemple : Uber

**Modules :**

* Gestion des trajets
* Géolocalisation
* Attribution chauffeur
* Paiement
* Historique
* Notation
* Gestion flotte
* Dashboard admin

---

## 🔟 Application **de gestion scolaire**

**Modules :**

* Gestion élèves
* Gestion enseignants
* Gestion classes
* Emploi du temps
* Notes & bulletins
* Paiement scolarité
* Communication parents
* Bibliothèque

---

Si tu veux, je peux te faire :

* 🔹 Une liste adaptée à ton projet Symfony multi-entreprise
* 🔹 Une architecture type base de données pour un type d’application précis
* 🔹 Une proposition modulaire pour une app en Côte d’Ivoire

Tu travailles sur quel type d’application actuellement ?









{% extends 'dashbord.html.twig' %}

{% block title %}Type de pièce | Transport{% endblock %}

{% block stylesheets %}
    {{ parent() }}

    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.6/css/buttons.bootstrap5.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/autofill/2.7.1/css/autoFill.bootstrap5.min.css"> {# Le remplissage automatique #}
{% endblock %}

{% block body %}
<h1 class="display-6 mb-3">Gestion des types de pièces</h1>
<a href="{{ path('typepiece.new') }}" class="btn btn-primary">Nouveau</a>
<table class="table table-striped" id="table">
    <thead>
        <tr>
            <th>Id</th>
            <th>Libellé</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>
{% endblock %}

{% block javascripts %}
    {{ parent() }}

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.7/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.7/js/dataTables.bootstrap5.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/3.2.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.6/js/buttons.bootstrap5.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <script src="https://cdn.datatables.net/buttons/3.2.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.6/js/buttons.print.min.js"></script> {# L'impression #}

    <script src="https://cdn.datatables.net/autofill/2.7.1/js/dataTables.autoFill.min.js"></script> {# Le rempli.. #}
    <script src="https://cdn.datatables.net/autofill/2.7.1/js/autoFill.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.6/js/buttons.colVis.min.js"></script> {# Le contrôle de la visibilité des colonnes #}

    {# 
        - On peut exclure par classe css '<th class="no-export">Actions</th>' - 'columns: ':not(.no-export)''
    #}
    <script>
        $(document).ready(function() {
            $('#table').DataTable({
                ajax: "{{ path('typepiece.data') }}",
                columns: [
                    { data: 'id' },
                    { data: 'libelle' },
                    { data: 'actions', orderable: false, searchable: false }
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.8/i18n/fr-FR.json"
                },
                dom: 'Bfrtip', // Important pour exporter
                buttons: [
                    {
                        extend: 'copyHtml5',
                        text: 'Copier',
                        title: 'Liste des types de pièces',
                        exportOptions: {
                            columns: [0, 1]
                        },
                        /* - Permet de personnalisé les colonnes
                            customize: function(doc) {
                                doc.styles.tableHeader.alignment = 'left';
                                doc.content[1].table.widths = ['20%', '80%'];
                            }
                        */
                    },
                    {
                        extend: 'excelHtml5',
                        text: 'Excel',
                        title: 'Liste des types de pièces',
                        exportOptions: {
                            columns: [0, 1]
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: 'PDF',
                        title: 'Liste des types de pièces',
                        exportOptions: {
                            columns: [0, 1]
                        }
                    },
                    {
                        extend: 'print',
                        text: 'Imprimer',
                        title: 'Liste des types de pièces',
                        exportOptions: {
                            columns: [0, 1]
                        }
                    }
                ]
            })
        })
    </script>
{% endblock %}






Solution 4 — Styliser Tom Select avec des classes Tailwind (custom)
Si tu veux un contrôle total avec Tailwind, initialise Tom Select avec des classes personnalisées :
jsnew TomSelect('#mon-select', {
  render: {
    option: function(data, escape) {
      return `<div class="px-3 py-2 hover:bg-blue-50 cursor-pointer text-sm">${escape(data.text)}</div>`;
    },
    item: function(data, escape) {
      return `<div class="bg-blue-100 text-blue-800 rounded px-2 py-0.5 text-sm">${escape(data.text)}</div>`;
    }
  }
});

Recommandation
La solution la plus simple et propre est la Solution 1 : importer le CSS de Tom Select après Tailwind. Ça évite les conflits et tu peux ensuite surcharger les styles si nécessaire.
Tu veux que je te crée un exemple complet avec Tom Select stylisé pour bien s'intégrer à Tailwind ?






La `role_hierarchy` de Symfony permet de définir qu'un rôle **hérite automatiquement** des permissions d'un autre rôle. Concrètement :

```yaml
role_hierarchy:
    ROLE_ADMIN: [ROLE_AGENT]
```

Ça veut dire que tout utilisateur avec `ROLE_ADMIN` a **automatiquement** aussi `ROLE_AGENT`, sans qu'on ait besoin de lui attribuer les deux dans la base de données.

---

### Exemple concret avec notre hiérarchie

```yaml
role_hierarchy:
    ROLE_OPERATEUR:   [ROLE_USER]
    ROLE_AGENT:       [ROLE_OPERATEUR]
    ROLE_ADMIN:       [ROLE_AGENT]
    ROLE_SUPER_ADMIN: [ROLE_ADMIN]
```

Un utilisateur stocké en base avec juste `["ROLE_ADMIN"]` aura en réalité :

```
ROLE_ADMIN
    → ROLE_AGENT (hérité)
        → ROLE_OPERATEUR (hérité)
            → ROLE_USER (hérité)
```

Donc `is_granted('ROLE_AGENT')` retournera `true` pour un admin, sans qu'on ait besoin de vérifier manuellement tous les rôles dans les voters.






use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\ApiResource;

#[ApiResource(
    operations: [
        new Get(
            security: "
                object.getEntreprise() 
                and user.getEntreprise()
                and (
                    object.getEntreprise().getId() == user.getEntreprise().getId()
                    or object.getId() == user.getId()
                )
            "
        ),

        new Put(
            security: "
                (
                    object.getEntreprise()
                    and user.getEntreprise()
                    and object.getEntreprise().getId() == user.getEntreprise().getId()
                    and is_granted('ROLE_AGENT')
                )
                or object.getId() == user.getId()
            "
        ),

        new Patch(
            security: "
                (
                    object.getEntreprise()
                    and user.getEntreprise()
                    and object.getEntreprise().getId() == user.getEntreprise().getId()
                    and is_granted('ROLE_AGENT')
                )
                or object.getId() == user.getId()
            "
        ),

        new Delete(
            security: "
                object.getEntreprise()
                and user.getEntreprise()
                and object.getEntreprise().getId() == user.getEntreprise().getId()
                and is_granted('ROLE_ADMIN')
                and not ('ROLE_ADMIN' in object.getRoles())
            "
        )
    ]
)]
class User
{
}




**Contacter via WhatsApp**
- On a plusieurs solutions pour intégrer ce système
    > Sol 1 : Par lien whatsapp, fournit une url qui ouvre une conversation avec un numéro donné avec ce format `https://wa.me/<NUMERO_E164>?text=<MESSAGE_URL_ENCODE>`
        > E.164 → `2250700000000`
        > Numéro CI : `+2250700000000`
        > MESSAGE_URL_ENCODE On encode le message avec `urlencode`


## 5️⃣ Variante mobile / desktop (UX avancée)

```php
$baseUrl = $request->headers->get('User-Agent') && str_contains(
    $request->headers->get('User-Agent'),
    'Mobile'
)
    ? 'https://wa.me/'
    : 'https://web.whatsapp.com/send?phone=';

$url = $baseUrl . '2250700000000&text=' . urlencode($message);
```


Principe technique (TEL URI)
Le standard universel est le **protocole `tel:`**
<a href="tel:+2250700000000">+225 07 00 00 00 00</a>

📱 Mobile → ouvre l’application téléphone
💻 Desktop → ouvre Skype / app VoIP / ou rien selon OS


## 6️⃣ UX avancée : mobile / desktop

### 🔹 Détection simple

```twig
{% set isMobile = app.request.headers.get('User-Agent') matches '/Mobile|Android|iPhone/i' %}
```

```twig
{% if isMobile %}
    <a href="{{ phone.telLink }}" class="btn btn-success">
        📞 Appeler maintenant
    </a>
{% else %}
    <p>
        📞 Appelez-nous au :
        <strong>{{ phone.display }}</strong>
    </p>
{% endif %}
```


## 10️⃣ BONUS : Click-to-call avec VoIP (pro)

Si tu veux :

* appeler depuis le navigateur
* centre d’appel
* enregistrement des appels

👉 Solutions :

* **Twilio Voice**
* **Vonage**
* **SIP.js + Asterisk**




            /*
                ->add('city', EntityType::class, [
                    'class' => City::class,
                    'choice_label' => 'name',
                    'placeholder' => 'Rechercher par ville',
                    'required' => false,
                    'label' => false,
                    'attr' => [
                        'class' => 'tom-select'
                    ]
                ])

                if($search->getCity()) {
                    $builder = $builder
                        ->andWhere('p.city = :city')
                        ->setParameter('city', $search->getCity());
                }

            ->add('city', EntityType::class, [
                'class' => City::class,
                'choice_label' => 'name',
                'placeholder' => 'Rechercher par ville',
                'required' => false,
                'label' => false,
                'attr' => [
                    'class' => 'tom-select'
                ]
            ])








public function findByRadius(
    float $lat,
    float $lng,
    int $radiusKm
): array {
    $qb = $this->createQueryBuilder('b');

    $qb
        ->addSelect(
            '(6371 * acos(
                cos(radians(:lat))
                * cos(radians(b.latitude))
                * cos(radians(b.longitude) - radians(:lng))
                + sin(radians(:lat))
                * sin(radians(b.latitude))
            )) AS HIDDEN distance'
        )
        ->having('distance <= :radius')
        ->setParameter('lat', $lat)
        ->setParameter('lng', $lng)
        ->setParameter('radius', $radiusKm)
        ->orderBy('distance', 'ASC');

    return $qb->getQuery()->getResult();
}

            /*
                ->add('city', EntityType::class, [
                    'class' => City::class,
                    'choice_label' => 'name',
                    'attr' => [
                        'tom-select' => 'tom-select'
                    ]
                ])
            */


Le type enum peut remplacer ManyToOne ou le ManyToMany utile pour les listes fermé







-- Filter.js
        try {
            const response = await fetch(url.split('?')[0] + '?' + params.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest' // On va faire un appel ajax
                }
            })
            if(response.status >= 200 && response.status < 300) {
                const data = await response.json()
                // this.content.innerHTML = data.content
                this.flipContent(data.content, append)
                if(!this.moreNav) {
                    this.paginnation = data.paginnation
                } else if(this.page === data.pages) {
                    this.paginnation.style.display = 'none'
                } else {
                    this.paginnation.style.display = 'block'
                }

                params.delete('ajax') // Avant de remplacer l'url je supprime le ajax
                history.replaceState({}, '', url.split('?')[0] + '?' + params.toString())
                // history.replaceState({}, '', url)
                // On remplace l'url .. On utilise replace au lieu de push car ç'est meilleur au niveau de l'expérience utilisateur en cas de retour dans le navigateur il ne reviendra pas sur les recherche précédentes mais sur la page précédente
            } else {
                console.error(response) // On peut mettre un élément d'interface pour une alerte
            }
        } catch(error) {
            console.error(error) // On peut mettre un élément d'interface pour une alerte
        } finally {
            this.hideLoader()
        }




Parfait ! On peut améliorer ton code pour gérer deux choses :

1. **Afficher un message “Aucun résultat trouvé”** si la recherche ne renvoie rien.
2. **Cacher le bouton “Voir plus”** si la recherche ne renvoie aucun résultat.

Voici comment tu peux modifier la méthode `loadUrl()` et `flipContent()` pour gérer ça proprement :

```js
async loadUrl(url, append = false) {
    this.showLoader()

    const params = new URLSearchParams(url.split('?')[1] || '')
    params.set('ajax', 1)

    try {
        const response = await fetch(url.split('?')[0] + '?' + params.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })

        if(response.ok) {
            const data = await response.json()

            // Si aucun contenu n'est renvoyé, on affiche un message
            if(!data.content || data.content.trim() === '') {
                this.content.innerHTML = `<p class="no-results">Aucun résultat trouvé</p>`
                if(this.moreNav) {
                    this.paginnation.style.display = 'none' // on cache le bouton voir plus
                }
            } else {
                this.flipContent(data.content, append)

                if(!this.moreNav) {
                    this.paginnation = data.paginnation
                } else if(this.page === data.pages) {
                    this.paginnation.style.display = 'none'
                } else {
                    this.paginnation.style.display = 'block'
                }
            }

            params.delete('ajax')
            history.replaceState({}, '', url.split('?')[0] + '?' + params.toString())
        } else {
            console.error(response)
            this.content.innerHTML = `<p class="no-results">Erreur lors de la récupération des données</p>`
            this.paginnation.style.display = 'none'
        }
    } catch (error) {
        console.error(error)
        this.content.innerHTML = `<p class="no-results">Erreur lors de la récupération des données</p>`
        this.paginnation.style.display = 'none'
    } finally {
        this.hideLoader()
    }
}
```

### Explications :

* `if(!data.content || data.content.trim() === '')` → si le serveur ne renvoie rien, on affiche un paragraphe “Aucun résultat trouvé”.
* `this.paginnation.style.display = 'none'` → on cache le bouton “Voir plus” s’il n’y a pas de résultats.
* `finally { this.hideLoader() }` → on est sûr de toujours cacher le loader, même en cas d’erreur réseau.

---

Si tu veux, je peux aussi te montrer **une version plus élégante** où le message “Aucun résultat trouvé” utilise la même animation que les contenus existants, donc ça reste cohérent avec ton `flip-toolkit`.

Veux‑tu que je fasse cette version animée ?










- !! champs personnalisés sur les produits..




        ->add('save_and_new', SubmitType::class, [
            'label' => 'Enregistrer & Nouveau',
            'attr' => ['class' => 'bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded ml-2'],
        ])
        if ($form->get('save_and_new')->isClicked()) {
            return $this->redirectToRoute('your_entity_new');
        }

bin/console make:state-provider
 /*
        - L'option 'controller' permet de remplacer complètement le comportement automatique de ApiPlatform et nous donne le contrôle total de la requête
        - !! 'provider' !! de personnaliser la récupération 'get', 'getCollection' et aussi charger les données depuis Doctrine, une api externe ou un microservice
            - 
        - !! 'processor' !! personnaliser la persistance 'écriture'
    */


# 📌 Cycle API Platform simplifié

### 🔹 GET

`Provider → Extension → Serializer → Response`

### 🔹 POST / PUT / PATCH

`Denormalizer → Validation → Processor → Response`












final class Version20260820120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Permission Ticket DESISTER : séparation du remboursement et de la modification';
    }

    public function up(Schema $schema): void
    {
        // 'created_at' est NOT NULL sur EntityBase ; on reprend celui de la permission d'origine.
        $this->addSql(<<<'SQL'
            INSERT INTO permission (entity, action, role_id, identreprise, created_at, etatdelete)
            SELECT p.entity, 'DESISTER', p.role_id, p.identreprise, NOW(), 0
            FROM permission p
            WHERE p.entity = 'Ticket'
              AND p.action = 'MODIFIER'
              AND NOT EXISTS (
                  SELECT 1 FROM (SELECT * FROM permission) d
                  WHERE d.entity = 'Ticket'
                    AND d.action = 'DESISTER'
                    AND d.role_id <=> p.role_id
                    AND d.identreprise <=> p.identreprise
              )
        SQL);
    }


 public function up(Schema $schema): void
    {
        foreach (self::REPRISES as $entite => $action) {
            // 'created_at' est NOT NULL sur EntityBase. La sous-requête enveloppée dans un SELECT
            // contourne l'interdiction MySQL de lire la table que l'on insère.
            $this->addSql(
                <<<'SQL'
                    INSERT INTO permission (entity, action, role_id, identreprise, created_at, etatdelete)
                    SELECT p.entity, :action, p.role_id, p.identreprise, NOW(), 0
                    FROM permission p
                    WHERE p.entity = :entite
                      AND p.action = 'MODIFIER'
                      AND NOT EXISTS (
                          SELECT 1 FROM (SELECT * FROM permission) d
                          WHERE d.entity = :entite
                            AND d.action = :action
                            AND d.role_id <=> p.role_id
                            AND d.identreprise <=> p.identreprise
                      )
                SQL,
                ['entite' => $entite, 'action' => $action]
            );
        }
    }

    public function down(Schema $schema): void
    {
        foreach (self::REPRISES as $entite => $action) {
            $this->addSql(
                'DELETE FROM permission WHERE entity = :entite AND action = :action',
                ['entite' => $entite, 'action' => $action]
            );
        }
    }

    $this->addSql('ALTER TABLE arret CHANGE duree_depuis_origine_minutes duree_troncon_minutes INT DEFAULT NULL');

       public function getDescription(): string
    {
        return 'Arrêt : durée de trajet depuis l\'origine (heure de passage par arrêt)';
    }


    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activite (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, type VARCHAR(60) NOT NULL, libelle VARCHAR(255) NOT NULL, cibletype VARCHAR(50) DEFAULT NULL, cibleid INT DEFAULT NULL, identreprise INT DEFAULT NULL, auteur_id INT DEFAULT NULL, INDEX IDX_B875551560BB6FE6 (auteur_id), INDEX idx_activite_cible (cibletype, cibleid), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE approvisionnement (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, dateappro DATETIME NOT NULL, identreprise INT DEFAULT NULL, statut VARCHAR(20) DEFAULT \'VALIDE\' NOT NULL, fournisseur_id INT NOT NULL, INDEX IDX_516C3FAA670C757F (fournisseur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE arret (id INT AUTO_INCREMENT NOT NULL, ordre INT NOT NULL, identreprise INT DEFAULT NULL, ligne_id INT NOT NULL, gare_id INT NOT NULL, INDEX IDX_BBD1570E5A438E76 (ligne_id), INDEX IDX_BBD1570E63FD956 (gare_id), UNIQUE INDEX UNIQ_arret_ligne_gare (ligne_id, gare_id), UNIQUE INDEX UNIQ_arret_ligne_ordre (ligne_id, ordre), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE bagage (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, codebagage VARCHAR(255) NOT NULL, nature VARCHAR(255) NOT NULL, type VARCHAR(255) DEFAULT NULL, poids INT NOT NULL, montant INT NOT NULL, montantforce TINYINT NOT NULL, statut VARCHAR(255) DEFAULT NULL, identreprise INT DEFAULT NULL, voyage_id INT DEFAULT NULL, ticket_id INT NOT NULL, commercial_id INT DEFAULT NULL, garedepart_id INT DEFAULT NULL, garedescente_id INT DEFAULT NULL, tarifbagage_id INT DEFAULT NULL, INDEX IDX_A82C571568C9E5AF (voyage_id), INDEX IDX_A82C5715700047D2 (ticket_id), INDEX IDX_A82C57157854071C (commercial_id), INDEX IDX_A82C571516887400 (garedepart_id), INDEX IDX_A82C57154734D724 (garedescente_id), INDEX IDX_A82C5715938B927E (tarifbagage_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE beneficiaire (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, categorie VARCHAR(50) NOT NULL, contact VARCHAR(255) DEFAULT NULL, identreprise INT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE car (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, matricule VARCHAR(255) NOT NULL, nbrsiege INT DEFAULT NULL, datearrivee DATETIME NOT NULL, etat VARCHAR(20) NOT NULL, identreprise INT DEFAULT NULL, sieges_gauche INT NOT NULL, sieges_droite INT NOT NULL, plansieges JSON DEFAULT NULL, marque_id INT DEFAULT NULL, typevehicule_id INT DEFAULT NULL, modelvehicule_id INT DEFAULT NULL, INDEX IDX_773DE69D4827B9B2 (marque_id), INDEX IDX_773DE69D3402134C (typevehicule_id), INDEX IDX_773DE69D403ECEFE (modelvehicule_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE client (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, contact VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, fidelite TINYINT DEFAULT 0 NOT NULL, cartefidelite VARCHAR(50) DEFAULT NULL, dateadhesion DATETIME DEFAULT NULL, identreprise INT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE config_recette (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, courriershorsca TINYINT NOT NULL, identreprise INT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE config_remise (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, maxpourcentage INT DEFAULT NULL, identreprise INT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE courrier (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, codecourrier VARCHAR(255) NOT NULL, nomexpediteur VARCHAR(255) NOT NULL, contactexpediteur VARCHAR(255) NOT NULL, nomdestinataire VARCHAR(255) NOT NULL, contactdestinataire VARCHAR(255) NOT NULL, fraissuivi INT DEFAULT NULL, montant BIGINT NOT NULL, statut VARCHAR(50) NOT NULL, identreprise INT DEFAULT NULL, datelivraison DATETIME DEFAULT NULL, garedepart_id INT NOT NULL, garearrivee_id INT NOT NULL, voyage_id INT DEFAULT NULL, INDEX IDX_BEF47CAA16887400 (garedepart_id), INDEX IDX_BEF47CAAB466CD0 (garearrivee_id), INDEX IDX_BEF47CAA68C9E5AF (voyage_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE depannage (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, datedepannage DATETIME NOT NULL, lieudepannage VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, identreprise INT DEFAULT NULL, couttotal BIGINT DEFAULT NULL, statut VARCHAR(255) NOT NULL, car_id INT NOT NULL, typepanne_id INT NOT NULL, INDEX IDX_F3C7E6B1C3C6F69F (car_id), INDEX IDX_F3C7E6B139DE3B35 (typepanne_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE detailapprovisionnement (id INT AUTO_INCREMENT NOT NULL, quantite INT NOT NULL, prixunitaire INT NOT NULL, couttotal BIGINT DEFAULT NULL, approvisionnement_id INT NOT NULL, piece_id INT NOT NULL, INDEX IDX_DAA21B32AE741A98 (approvisionnement_id), INDEX IDX_DAA21B32C40FCFA8 (piece_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE detailcourrier (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, nature VARCHAR(255) NOT NULL, designation VARCHAR(255) DEFAULT NULL, emballage VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, poids INT DEFAULT NULL, valeur BIGINT NOT NULL, montant BIGINT NOT NULL, statut VARCHAR(80) NOT NULL, identreprise INT DEFAULT NULL, courrier_id INT NOT NULL, tarifcourrier_id INT DEFAULT NULL, INDEX IDX_AD731D028BF41DC7 (courrier_id), INDEX IDX_AD731D02B952E85D (tarifcourrier_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE detaildepannage (id INT AUTO_INCREMENT NOT NULL, quantite INT NOT NULL, prixunitaire INT DEFAULT NULL, depannage_id INT NOT NULL, piece_id INT NOT NULL, INDEX IDX_2BD94A0AAFF9529D (depannage_id), INDEX IDX_2BD94A0AC40FCFA8 (piece_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE detailpersonnel (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, motif VARCHAR(255) NOT NULL, identreprise INT DEFAULT NULL, personnel_id INT NOT NULL, depannage_id INT DEFAULT NULL, voyage_id INT DEFAULT NULL, INDEX IDX_7EA25F651C109075 (personnel_id), INDEX IDX_7EA25F65AFF9529D (depannage_id), INDEX IDX_7EA25F6568C9E5AF (voyage_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE entreprise (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, slug VARCHAR(255) DEFAULT NULL, contact1 VARCHAR(255) NOT NULL, contact2 VARCHAR(255) DEFAULT NULL, adresse VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, anneecreation DATE DEFAULT NULL, sigle VARCHAR(255) DEFAULT NULL, siteweb VARCHAR(255) DEFAULT NULL, rccm VARCHAR(255) DEFAULT NULL, banque VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, centreimpot VARCHAR(255) DEFAULT NULL, tauxtva INT DEFAULT NULL, statut VARCHAR(255) DEFAULT NULL, image_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_D19FA60989D9B62 (slug), INDEX IDX_D19FA603DA5256D (image_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE fournisseur (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, contact VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, adresse VARCHAR(255) NOT NULL, pays VARCHAR(255) NOT NULL, identreprise INT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE gare (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, chefgare VARCHAR(255) NOT NULL, libelle VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, contact1 VARCHAR(255) NOT NULL, contact2 VARCHAR(255) DEFAULT NULL, identreprise INT DEFAULT NULL, statut VARCHAR(255) NOT NULL, datecreation DATETIME DEFAULT NULL, ville_id INT DEFAULT NULL, INDEX IDX_EE713F12A73F0036 (ville_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE inventaire (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, typemouvement VARCHAR(255) NOT NULL, quantite INT NOT NULL, datemouvement DATETIME NOT NULL, identreprise INT DEFAULT NULL, reference_type VARCHAR(255) NOT NULL, referenceid INT DEFAULT NULL, piece_id INT NOT NULL, auteur_id INT DEFAULT NULL, INDEX IDX_338920E0C40FCFA8 (piece_id), INDEX IDX_338920E060BB6FE6 (auteur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE ligne (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, codeligne VARCHAR(255) NOT NULL, libelle VARCHAR(255) DEFAULT NULL, heuredepart TIME DEFAULT NULL, identreprise INT DEFAULT NULL, gareorigine_id INT NOT NULL, gareterminus_id INT NOT NULL, INDEX IDX_57F0DB83E1318B1C (gareorigine_id), INDEX IDX_57F0DB8364815E19 (gareterminus_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE maintenance (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, actif TINYINT NOT NULL, message LONGTEXT DEFAULT NULL, depuis DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE marque (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, identreprise INT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE marquepiece (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, identreprise INT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE media_object (id INT AUTO_INCREMENT NOT NULL, file_path VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE model (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, identreprise INT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE modelvehicule (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, identreprise INT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE parametre_reservation (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, delai_expiration_minutes INT DEFAULT 120 NOT NULL, penalite_type VARCHAR(20) DEFAULT \'AUCUNE\' NOT NULL, penalite_valeur INT DEFAULT 0 NOT NULL, fenetre_regularisation_jours INT DEFAULT 7 NOT NULL, identreprise INT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE password_reset_token (id INT AUTO_INCREMENT NOT NULL, token VARCHAR(255) NOT NULL, expires_at DATETIME NOT NULL, used_at DATETIME DEFAULT NULL, user_id INT NOT NULL, INDEX IDX_6B7BA4B6A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE permission (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, entity VARCHAR(255) NOT NULL, action VARCHAR(255) NOT NULL, identreprise INT DEFAULT NULL, role_id INT DEFAULT NULL, INDEX IDX_E04992AAD60322AC (role_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE personnel (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, contact VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, identreprise INT DEFAULT NULL, statut VARCHAR(50) DEFAULT NULL, dateembauche DATETIME DEFAULT NULL, typepersonnel_id INT NOT NULL, image_id INT DEFAULT NULL, INDEX IDX_A6BCF3DE9B4F51A (typepersonnel_id), INDEX IDX_A6BCF3DE3DA5256D (image_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE piece (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, identreprise INT DEFAULT NULL, stockinitial INT NOT NULL, prixunitaire INT NOT NULL, seuilstock INT DEFAULT NULL, typepiece_id INT DEFAULT NULL, marquepiece_id INT DEFAULT NULL, model_id INT DEFAULT NULL, image_id INT DEFAULT NULL, INDEX IDX_44CA0B2398664120 (typepiece_id), INDEX IDX_44CA0B23699D8DC0 (marquepiece_id), INDEX IDX_44CA0B237975B7E7 (model_id), INDEX IDX_44CA0B233DA5256D (image_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE programme_fidelite (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, seuil INT DEFAULT 10 NOT NULL, recompense_pourcentage INT DEFAULT 100 NOT NULL, actif TINYINT DEFAULT 1 NOT NULL, identreprise INT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE refresh_tokens (refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid DATETIME NOT NULL, id INT AUTO_INCREMENT NOT NULL, UNIQUE INDEX UNIQ_9BACE7E1C74F2195 (refresh_token), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE reservation (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, nomclient VARCHAR(255) DEFAULT NULL, contactclient VARCHAR(255) DEFAULT NULL, prix INT NOT NULL, statut VARCHAR(20) DEFAULT \'EN_ATTENTE\' NOT NULL, dateexpiration DATETIME NOT NULL, source VARCHAR(20) DEFAULT \'GUICHET\' NOT NULL, etatpaiement VARCHAR(30) DEFAULT \'EN_ATTENTE_PAIEMENT\' NOT NULL, referencepaiement VARCHAR(255) DEFAULT NULL, datepaiement DATETIME DEFAULT NULL, penalitemontant INT DEFAULT 0 NOT NULL, montantcomplement INT DEFAULT 0 NOT NULL, identreprise INT DEFAULT NULL, client_id INT DEFAULT NULL, voyage_id INT NOT NULL, gare_id INT NOT NULL, garedescente_id INT NOT NULL, ticket_id INT DEFAULT NULL, INDEX IDX_42C8495519EB6921 (client_id), INDEX IDX_42C8495568C9E5AF (voyage_id), INDEX IDX_42C8495563FD956 (gare_id), INDEX IDX_42C849554734D724 (garedescente_id), INDEX IDX_42C84955700047D2 (ticket_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE role (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, identreprise INT DEFAULT NULL, typerole VARCHAR(255) DEFAULT NULL, gare_id INT DEFAULT NULL, INDEX IDX_57698A6A63FD956 (gare_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE siege (id INT AUTO_INCREMENT NOT NULL, numero INT NOT NULL, rangee INT DEFAULT NULL, colonne INT DEFAULT NULL, cote VARCHAR(255) DEFAULT NULL, identreprise INT DEFAULT NULL, car_id INT NOT NULL, INDEX IDX_6706B4F7C3C6F69F (car_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tarif (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, montant INT NOT NULL, identreprise INT DEFAULT NULL, garedepart_id INT NOT NULL, garearrivee_id INT NOT NULL, INDEX IDX_E7189C916887400 (garedepart_id), INDEX IDX_E7189C9B466CD0 (garearrivee_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tarifbagage (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, poidsmin INT NOT NULL, poidsmax INT DEFAULT NULL, montant INT NOT NULL, identreprise INT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tarifcourrier (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, valeurmin INT NOT NULL, valeurmax INT DEFAULT NULL, montanttaxe INT NOT NULL, identreprise INT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE ticket (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, identreprise INT DEFAULT NULL, nomclient VARCHAR(255) DEFAULT NULL, contactclient VARCHAR(255) DEFAULT NULL, prix INT NOT NULL, codeticket VARCHAR(255) NOT NULL, remise INT DEFAULT 0 NOT NULL, fidelite_recompense TINYINT DEFAULT 0 NOT NULL, statut VARCHAR(20) DEFAULT \'VALIDE\' NOT NULL, datedesistement DATETIME DEFAULT NULL, motifdesistement VARCHAR(255) DEFAULT NULL, voyage_id INT NOT NULL, client_id INT DEFAULT NULL, siege_id INT NOT NULL, gare_id INT NOT NULL, garedescente_id INT NOT NULL, garedescentereelle_id INT DEFAULT NULL, commercial_id INT DEFAULT NULL, reservation_id INT DEFAULT NULL, beneficiaire_id INT DEFAULT NULL, ticket_origine_id INT DEFAULT NULL, INDEX IDX_97A0ADA368C9E5AF (voyage_id), INDEX IDX_97A0ADA319EB6921 (client_id), INDEX IDX_97A0ADA3BF006E8B (siege_id), INDEX IDX_97A0ADA363FD956 (gare_id), INDEX IDX_97A0ADA34734D724 (garedescente_id), INDEX IDX_97A0ADA328DC8408 (garedescentereelle_id), INDEX IDX_97A0ADA37854071C (commercial_id), INDEX IDX_97A0ADA3B83297E7 (reservation_id), INDEX IDX_97A0ADA35AF81F68 (beneficiaire_id), INDEX IDX_97A0ADA396140BDA (ticket_origine_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE typepanne (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, identreprise INT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE typepersonnel (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, identreprise INT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE typepiece (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, identreprise INT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE typevehicule (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, identreprise INT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, avatar VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, statut VARCHAR(50) NOT NULL, is_founder TINYINT DEFAULT NULL, entreprise_id INT DEFAULT NULL, gare_id INT DEFAULT NULL, INDEX IDX_8D93D649A4AEAFEA (entreprise_id), INDEX IDX_8D93D64963FD956 (gare_id), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_role (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, identreprise INT DEFAULT NULL, usere_id INT DEFAULT NULL, role_id INT DEFAULT NULL, INDEX IDX_2DE8C6A312C1BC7E (usere_id), INDEX IDX_2DE8C6A3D60322AC (role_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE ville (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, identreprise INT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE voyage (created_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_from_ip VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_from_ip VARCHAR(255) DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, deleted_by INT DEFAULT NULL, etatdelete TINYINT DEFAULT 0, id INT AUTO_INCREMENT NOT NULL, codevoyage VARCHAR(255) NOT NULL, provenance VARCHAR(255) NOT NULL, destination VARCHAR(255) NOT NULL, datedepartprevue DATETIME NOT NULL, datearriveeprevue DATETIME DEFAULT NULL, datedepartreelle DATETIME DEFAULT NULL, datearriveereelle DATETIME DEFAULT NULL, placesprevues INT DEFAULT NULL, identreprise INT DEFAULT NULL, placestotal INT DEFAULT NULL, ligne_id INT NOT NULL, car_id INT DEFAULT NULL, commercial_id INT DEFAULT NULL, garecourante_id INT DEFAULT NULL, gareprovenance_id INT DEFAULT NULL, INDEX IDX_3F9D89555A438E76 (ligne_id), INDEX IDX_3F9D8955C3C6F69F (car_id), INDEX IDX_3F9D89557854071C (commercial_id), INDEX IDX_3F9D89554E693E42 (garecourante_id), INDEX IDX_3F9D8955662BF1CE (gareprovenance_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE activite ADD CONSTRAINT FK_B875551560BB6FE6 FOREIGN KEY (auteur_id) REFERENCES `user` (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE approvisionnement ADD CONSTRAINT FK_516C3FAA670C757F FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id)');
        $this->addSql('ALTER TABLE arret ADD CONSTRAINT FK_BBD1570E5A438E76 FOREIGN KEY (ligne_id) REFERENCES ligne (id)');
        $this->addSql('ALTER TABLE arret ADD CONSTRAINT FK_BBD1570E63FD956 FOREIGN KEY (gare_id) REFERENCES gare (id)');
        $this->addSql('ALTER TABLE bagage ADD CONSTRAINT FK_A82C571568C9E5AF FOREIGN KEY (voyage_id) REFERENCES voyage (id)');
        $this->addSql('ALTER TABLE bagage ADD CONSTRAINT FK_A82C5715700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket (id)');
        $this->addSql('ALTER TABLE bagage ADD CONSTRAINT FK_A82C57157854071C FOREIGN KEY (commercial_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE bagage ADD CONSTRAINT FK_A82C571516887400 FOREIGN KEY (garedepart_id) REFERENCES gare (id)');
        $this->addSql('ALTER TABLE bagage ADD CONSTRAINT FK_A82C57154734D724 FOREIGN KEY (garedescente_id) REFERENCES gare (id)');
        $this->addSql('ALTER TABLE bagage ADD CONSTRAINT FK_A82C5715938B927E FOREIGN KEY (tarifbagage_id) REFERENCES tarifbagage (id)');
        $this->addSql('ALTER TABLE car ADD CONSTRAINT FK_773DE69D4827B9B2 FOREIGN KEY (marque_id) REFERENCES marque (id)');
        $this->addSql('ALTER TABLE car ADD CONSTRAINT FK_773DE69D3402134C FOREIGN KEY (typevehicule_id) REFERENCES typevehicule (id)');
        $this->addSql('ALTER TABLE car ADD CONSTRAINT FK_773DE69D403ECEFE FOREIGN KEY (modelvehicule_id) REFERENCES modelvehicule (id)');
        $this->addSql('ALTER TABLE courrier ADD CONSTRAINT FK_BEF47CAA16887400 FOREIGN KEY (garedepart_id) REFERENCES gare (id)');
        $this->addSql('ALTER TABLE courrier ADD CONSTRAINT FK_BEF47CAAB466CD0 FOREIGN KEY (garearrivee_id) REFERENCES gare (id)');
        $this->addSql('ALTER TABLE courrier ADD CONSTRAINT FK_BEF47CAA68C9E5AF FOREIGN KEY (voyage_id) REFERENCES voyage (id)');
        $this->addSql('ALTER TABLE depannage ADD CONSTRAINT FK_F3C7E6B1C3C6F69F FOREIGN KEY (car_id) REFERENCES car (id)');
        $this->addSql('ALTER TABLE depannage ADD CONSTRAINT FK_F3C7E6B139DE3B35 FOREIGN KEY (typepanne_id) REFERENCES typepanne (id)');
        $this->addSql('ALTER TABLE detailapprovisionnement ADD CONSTRAINT FK_DAA21B32AE741A98 FOREIGN KEY (approvisionnement_id) REFERENCES approvisionnement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE detailapprovisionnement ADD CONSTRAINT FK_DAA21B32C40FCFA8 FOREIGN KEY (piece_id) REFERENCES piece (id)');
        $this->addSql('ALTER TABLE detailcourrier ADD CONSTRAINT FK_AD731D028BF41DC7 FOREIGN KEY (courrier_id) REFERENCES courrier (id)');
        $this->addSql('ALTER TABLE detailcourrier ADD CONSTRAINT FK_AD731D02B952E85D FOREIGN KEY (tarifcourrier_id) REFERENCES tarifcourrier (id)');
        $this->addSql('ALTER TABLE detaildepannage ADD CONSTRAINT FK_2BD94A0AAFF9529D FOREIGN KEY (depannage_id) REFERENCES depannage (id)');
        $this->addSql('ALTER TABLE detaildepannage ADD CONSTRAINT FK_2BD94A0AC40FCFA8 FOREIGN KEY (piece_id) REFERENCES piece (id)');
        $this->addSql('ALTER TABLE detailpersonnel ADD CONSTRAINT FK_7EA25F651C109075 FOREIGN KEY (personnel_id) REFERENCES personnel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE detailpersonnel ADD CONSTRAINT FK_7EA25F65AFF9529D FOREIGN KEY (depannage_id) REFERENCES depannage (id)');
        $this->addSql('ALTER TABLE detailpersonnel ADD CONSTRAINT FK_7EA25F6568C9E5AF FOREIGN KEY (voyage_id) REFERENCES voyage (id)');
        $this->addSql('ALTER TABLE entreprise ADD CONSTRAINT FK_D19FA603DA5256D FOREIGN KEY (image_id) REFERENCES media_object (id)');
        $this->addSql('ALTER TABLE gare ADD CONSTRAINT FK_EE713F12A73F0036 FOREIGN KEY (ville_id) REFERENCES ville (id)');
        $this->addSql('ALTER TABLE inventaire ADD CONSTRAINT FK_338920E0C40FCFA8 FOREIGN KEY (piece_id) REFERENCES piece (id)');
        $this->addSql('ALTER TABLE inventaire ADD CONSTRAINT FK_338920E060BB6FE6 FOREIGN KEY (auteur_id) REFERENCES `user` (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ligne ADD CONSTRAINT FK_57F0DB83E1318B1C FOREIGN KEY (gareorigine_id) REFERENCES gare (id)');
        $this->addSql('ALTER TABLE ligne ADD CONSTRAINT FK_57F0DB8364815E19 FOREIGN KEY (gareterminus_id) REFERENCES gare (id)');
        $this->addSql('ALTER TABLE password_reset_token ADD CONSTRAINT FK_6B7BA4B6A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE permission ADD CONSTRAINT FK_E04992AAD60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
        $this->addSql('ALTER TABLE personnel ADD CONSTRAINT FK_A6BCF3DE9B4F51A FOREIGN KEY (typepersonnel_id) REFERENCES typepersonnel (id)');
        $this->addSql('ALTER TABLE personnel ADD CONSTRAINT FK_A6BCF3DE3DA5256D FOREIGN KEY (image_id) REFERENCES media_object (id)');
        $this->addSql('ALTER TABLE piece ADD CONSTRAINT FK_44CA0B2398664120 FOREIGN KEY (typepiece_id) REFERENCES typepiece (id)');
        $this->addSql('ALTER TABLE piece ADD CONSTRAINT FK_44CA0B23699D8DC0 FOREIGN KEY (marquepiece_id) REFERENCES marquepiece (id)');
        $this->addSql('ALTER TABLE piece ADD CONSTRAINT FK_44CA0B237975B7E7 FOREIGN KEY (model_id) REFERENCES model (id)');
        $this->addSql('ALTER TABLE piece ADD CONSTRAINT FK_44CA0B233DA5256D FOREIGN KEY (image_id) REFERENCES media_object (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495519EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495568C9E5AF FOREIGN KEY (voyage_id) REFERENCES voyage (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495563FD956 FOREIGN KEY (gare_id) REFERENCES gare (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849554734D724 FOREIGN KEY (garedescente_id) REFERENCES gare (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket (id)');
        $this->addSql('ALTER TABLE role ADD CONSTRAINT FK_57698A6A63FD956 FOREIGN KEY (gare_id) REFERENCES gare (id)');
        $this->addSql('ALTER TABLE siege ADD CONSTRAINT FK_6706B4F7C3C6F69F FOREIGN KEY (car_id) REFERENCES car (id)');
        $this->addSql('ALTER TABLE tarif ADD CONSTRAINT FK_E7189C916887400 FOREIGN KEY (garedepart_id) REFERENCES gare (id)');
        $this->addSql('ALTER TABLE tarif ADD CONSTRAINT FK_E7189C9B466CD0 FOREIGN KEY (garearrivee_id) REFERENCES gare (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA368C9E5AF FOREIGN KEY (voyage_id) REFERENCES voyage (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA319EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3BF006E8B FOREIGN KEY (siege_id) REFERENCES siege (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA363FD956 FOREIGN KEY (gare_id) REFERENCES gare (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA34734D724 FOREIGN KEY (garedescente_id) REFERENCES gare (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA328DC8408 FOREIGN KEY (garedescentereelle_id) REFERENCES gare (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA37854071C FOREIGN KEY (commercial_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3B83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA35AF81F68 FOREIGN KEY (beneficiaire_id) REFERENCES beneficiaire (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA396140BDA FOREIGN KEY (ticket_origine_id) REFERENCES ticket (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D64963FD956 FOREIGN KEY (gare_id) REFERENCES gare (id)');
        $this->addSql('ALTER TABLE user_role ADD CONSTRAINT FK_2DE8C6A312C1BC7E FOREIGN KEY (usere_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user_role ADD CONSTRAINT FK_2DE8C6A3D60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
        $this->addSql('ALTER TABLE voyage ADD CONSTRAINT FK_3F9D89555A438E76 FOREIGN KEY (ligne_id) REFERENCES ligne (id)');
        $this->addSql('ALTER TABLE voyage ADD CONSTRAINT FK_3F9D8955C3C6F69F FOREIGN KEY (car_id) REFERENCES car (id)');
        $this->addSql('ALTER TABLE voyage ADD CONSTRAINT FK_3F9D89557854071C FOREIGN KEY (commercial_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE voyage ADD CONSTRAINT FK_3F9D89554E693E42 FOREIGN KEY (garecourante_id) REFERENCES gare (id)');
        $this->addSql('ALTER TABLE voyage ADD CONSTRAINT FK_3F9D8955662BF1CE FOREIGN KEY (gareprovenance_id) REFERENCES gare (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activite DROP FOREIGN KEY FK_B875551560BB6FE6');
        $this->addSql('ALTER TABLE approvisionnement DROP FOREIGN KEY FK_516C3FAA670C757F');
        $this->addSql('ALTER TABLE arret DROP FOREIGN KEY FK_BBD1570E5A438E76');
        $this->addSql('ALTER TABLE arret DROP FOREIGN KEY FK_BBD1570E63FD956');
        $this->addSql('ALTER TABLE bagage DROP FOREIGN KEY FK_A82C571568C9E5AF');
        $this->addSql('ALTER TABLE bagage DROP FOREIGN KEY FK_A82C5715700047D2');
        $this->addSql('ALTER TABLE bagage DROP FOREIGN KEY FK_A82C57157854071C');
        $this->addSql('ALTER TABLE bagage DROP FOREIGN KEY FK_A82C571516887400');
        $this->addSql('ALTER TABLE bagage DROP FOREIGN KEY FK_A82C57154734D724');
        $this->addSql('ALTER TABLE bagage DROP FOREIGN KEY FK_A82C5715938B927E');
        $this->addSql('ALTER TABLE car DROP FOREIGN KEY FK_773DE69D4827B9B2');
        $this->addSql('ALTER TABLE car DROP FOREIGN KEY FK_773DE69D3402134C');
        $this->addSql('ALTER TABLE car DROP FOREIGN KEY FK_773DE69D403ECEFE');
        $this->addSql('ALTER TABLE courrier DROP FOREIGN KEY FK_BEF47CAA16887400');
        $this->addSql('ALTER TABLE courrier DROP FOREIGN KEY FK_BEF47CAAB466CD0');
        $this->addSql('ALTER TABLE courrier DROP FOREIGN KEY FK_BEF47CAA68C9E5AF');
        $this->addSql('ALTER TABLE depannage DROP FOREIGN KEY FK_F3C7E6B1C3C6F69F');
        $this->addSql('ALTER TABLE depannage DROP FOREIGN KEY FK_F3C7E6B139DE3B35');
        $this->addSql('ALTER TABLE detailapprovisionnement DROP FOREIGN KEY FK_DAA21B32AE741A98');
        $this->addSql('ALTER TABLE detailapprovisionnement DROP FOREIGN KEY FK_DAA21B32C40FCFA8');
        $this->addSql('ALTER TABLE detailcourrier DROP FOREIGN KEY FK_AD731D028BF41DC7');
        $this->addSql('ALTER TABLE detailcourrier DROP FOREIGN KEY FK_AD731D02B952E85D');
        $this->addSql('ALTER TABLE detaildepannage DROP FOREIGN KEY FK_2BD94A0AAFF9529D');
        $this->addSql('ALTER TABLE detaildepannage DROP FOREIGN KEY FK_2BD94A0AC40FCFA8');
        $this->addSql('ALTER TABLE detailpersonnel DROP FOREIGN KEY FK_7EA25F651C109075');
        $this->addSql('ALTER TABLE detailpersonnel DROP FOREIGN KEY FK_7EA25F65AFF9529D');
        $this->addSql('ALTER TABLE detailpersonnel DROP FOREIGN KEY FK_7EA25F6568C9E5AF');
        $this->addSql('ALTER TABLE entreprise DROP FOREIGN KEY FK_D19FA603DA5256D');
        $this->addSql('ALTER TABLE gare DROP FOREIGN KEY FK_EE713F12A73F0036');
        $this->addSql('ALTER TABLE inventaire DROP FOREIGN KEY FK_338920E0C40FCFA8');
        $this->addSql('ALTER TABLE inventaire DROP FOREIGN KEY FK_338920E060BB6FE6');
        $this->addSql('ALTER TABLE ligne DROP FOREIGN KEY FK_57F0DB83E1318B1C');
        $this->addSql('ALTER TABLE ligne DROP FOREIGN KEY FK_57F0DB8364815E19');
        $this->addSql('ALTER TABLE password_reset_token DROP FOREIGN KEY FK_6B7BA4B6A76ED395');
        $this->addSql('ALTER TABLE permission DROP FOREIGN KEY FK_E04992AAD60322AC');
        $this->addSql('ALTER TABLE personnel DROP FOREIGN KEY FK_A6BCF3DE9B4F51A');
        $this->addSql('ALTER TABLE personnel DROP FOREIGN KEY FK_A6BCF3DE3DA5256D');
        $this->addSql('ALTER TABLE piece DROP FOREIGN KEY FK_44CA0B2398664120');
        $this->addSql('ALTER TABLE piece DROP FOREIGN KEY FK_44CA0B23699D8DC0');
        $this->addSql('ALTER TABLE piece DROP FOREIGN KEY FK_44CA0B237975B7E7');
        $this->addSql('ALTER TABLE piece DROP FOREIGN KEY FK_44CA0B233DA5256D');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495519EB6921');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495568C9E5AF');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495563FD956');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849554734D724');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955700047D2');
        $this->addSql('ALTER TABLE role DROP FOREIGN KEY FK_57698A6A63FD956');
        $this->addSql('ALTER TABLE siege DROP FOREIGN KEY FK_6706B4F7C3C6F69F');
        $this->addSql('ALTER TABLE tarif DROP FOREIGN KEY FK_E7189C916887400');
        $this->addSql('ALTER TABLE tarif DROP FOREIGN KEY FK_E7189C9B466CD0');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA368C9E5AF');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA319EB6921');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3BF006E8B');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA363FD956');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA34734D724');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA328DC8408');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA37854071C');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3B83297E7');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA35AF81F68');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA396140BDA');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649A4AEAFEA');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D64963FD956');
        $this->addSql('ALTER TABLE user_role DROP FOREIGN KEY FK_2DE8C6A312C1BC7E');
        $this->addSql('ALTER TABLE user_role DROP FOREIGN KEY FK_2DE8C6A3D60322AC');
        $this->addSql('ALTER TABLE voyage DROP FOREIGN KEY FK_3F9D89555A438E76');
        $this->addSql('ALTER TABLE voyage DROP FOREIGN KEY FK_3F9D8955C3C6F69F');
        $this->addSql('ALTER TABLE voyage DROP FOREIGN KEY FK_3F9D89557854071C');
        $this->addSql('ALTER TABLE voyage DROP FOREIGN KEY FK_3F9D89554E693E42');
        $this->addSql('ALTER TABLE voyage DROP FOREIGN KEY FK_3F9D8955662BF1CE');
        $this->addSql('DROP TABLE activite');
        $this->addSql('DROP TABLE approvisionnement');
        $this->addSql('DROP TABLE arret');
        $this->addSql('DROP TABLE bagage');
        $this->addSql('DROP TABLE beneficiaire');
        $this->addSql('DROP TABLE car');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE config_recette');
        $this->addSql('DROP TABLE config_remise');
        $this->addSql('DROP TABLE courrier');
        $this->addSql('DROP TABLE depannage');
        $this->addSql('DROP TABLE detailapprovisionnement');
        $this->addSql('DROP TABLE detailcourrier');
        $this->addSql('DROP TABLE detaildepannage');
        $this->addSql('DROP TABLE detailpersonnel');
        $this->addSql('DROP TABLE entreprise');
        $this->addSql('DROP TABLE fournisseur');
        $this->addSql('DROP TABLE gare');
        $this->addSql('DROP TABLE inventaire');
        $this->addSql('DROP TABLE ligne');
        $this->addSql('DROP TABLE maintenance');
        $this->addSql('DROP TABLE marque');
        $this->addSql('DROP TABLE marquepiece');
        $this->addSql('DROP TABLE media_object');
        $this->addSql('DROP TABLE model');
        $this->addSql('DROP TABLE modelvehicule');
        $this->addSql('DROP TABLE parametre_reservation');
        $this->addSql('DROP TABLE password_reset_token');
        $this->addSql('DROP TABLE permission');
        $this->addSql('DROP TABLE personnel');
        $this->addSql('DROP TABLE piece');
        $this->addSql('DROP TABLE programme_fidelite');
        $this->addSql('DROP TABLE refresh_tokens');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE siege');
        $this->addSql('DROP TABLE tarif');
        $this->addSql('DROP TABLE tarifbagage');
        $this->addSql('DROP TABLE tarifcourrier');
        $this->addSql('DROP TABLE ticket');
        $this->addSql('DROP TABLE typepanne');
        $this->addSql('DROP TABLE typepersonnel');
        $this->addSql('DROP TABLE typepiece');
        $this->addSql('DROP TABLE typevehicule');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE user_role');
        $this->addSql('DROP TABLE ville');
        $this->addSql('DROP TABLE voyage');
    }










- - **Symfony**
> NotCompromisedPassword a exigé symfony/http-client (installé), et je l'ai réglé en skipOnError — sur une connexion de terrain coupée, la vérification ne doit pas empêcher de créer un compte.
#[Assert\NotBlank(message: 'Le nouveau mot de passe est obligatoire')]
#[Assert\Length(min: 10, minMessage: 'Le mot de passe doit faire au moins {{ limit }} caractères')]
#[Assert\NotCompromisedPassword(
    message: 'Ce mot de passe figure dans des fuites de données connues : choisissez-en un autre',
    skipOnError: true // La vérification interroge un service distant : sur une connexion de terrain coupée, elle ne doit pas empêcher de créer un compte
)]
#[Assert\Regex(
    pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
    message: 'Le mot de passe doit contenir au moins une minuscule, une majuscule et un chiffre'
)]

`validator.yaml`
when@test:
    framework:
        validation:
            not_compromised_password: false

# Le bloc 'when@test' désactivait 'not_compromised_password', qui interrogeait un service distant pendant les tests. La contrainte n'est plus posée nulle part : le réglage n'avait donc plus d'objet.











Tu dois choisir les DLL compatibles avec :

* ta version de PHP (`php -v`)
* ton architecture (`x64` ou `x86`)
* Thread Safe (TS) ou Non-Thread Safe (NTS)

Par exemple, pour PHP 8.4 en x64 Thread Safe :

```ini
extension=php_sqlsrv_84_ts_x64.dll
extension=php_pdo_sqlsrv_84_ts_x64.dll
```

Place les DLL dans ton dossier `ext` de PHP, puis ajoute-les dans ton `php.ini`.

Vérifie ensuite :

```bash
php -m | findstr sqlsrv
```

Tu devrais voir :

```text
pdo_sqlsrv
sqlsrv
```

Si Doctrine est déjà installé, tu n'as normalement rien de particulier à ajouter.

Doctrine DBAL prend en charge SQL Server via le driver `pdo_sqlsrv`. La documentation de [Doctrine DBAL](https://www.doctrine-project.org/projects/doctrine-dbal/en/current/reference/configuration.html?utm_source=chatgpt.com) détaille les paramètres de connexion disponibles.

---

## 6. Attention aux migrations

C'est particulièrement important si ton projet était auparavant sous MySQL ou PostgreSQL.

SQL Server possède certaines différences concernant :

* les auto-increments → `IDENTITY`
* les types `BOOLEAN`
* `JSON`
* `UUID`
* les index
* les contraintes
* les types `DATETIME`
* les longueurs maximales de `VARCHAR`
* certaines syntaxes SQL spécifiques

Par exemple, il vaut mieux éviter d'écrire directement des requêtes SQL spécifiques à MySQL comme :

```sql
LIMIT 10
```

Doctrine générera automatiquement la syntaxe appropriée pour SQL Server lorsque tu utilises le QueryBuilder :

```php
$repository->createQueryBuilder('u')
    ->setMaxResults(10)
    ->getQuery()
    ->getResult();
```

**SQL Server - Microsoft**
- On..  plusieurs versions
    > `SQL Server sur site` la version qu'on peut installer en local ou sur hébergeur
    > `!! Azure` !! cloud
    > `!! Développeur` !! pour les développeurs, non `r` en production
    > `!! Express` !! gratuite et `r` à la production
- On a 3 types d'installation
    > `Basic` va l'installer avec les paramètres par défaut
    > `Custom` pour définir les paramètres
    > `Download Media` pour télécharger les fichiers et l'ins.. se fera après
- Pour avoir une interface graphique de la base de données on a `Install SSMS` SQL Server Management Studio
    > Le nom du server ; Le nom de ma machine\le nom de l'instance
    > Chiffrer : Facultatif sinon on aura une erreur de chaîne de certificat

`microsoft.com/en-in/sql-server/sql-server-downloads`
`learn.microsoft.com/fr-fr/ssms/install/install`

MSSQLSERVER == L'instance par défaut
SQLSERVERLESS
User : sa
password : Bakayoko10

- Pour l'utiliser via `php`
    > On.. télécharger les extensions `` et ``
