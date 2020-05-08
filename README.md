# wild-series

## Créer nouveau projet Symfony

```symfony new --full <name_project> --version=lts```

## Installation de Webpack

![Documentation Webpack/Symfony](https://symfony.com/doc/current/frontend/encore/installation.html)

#### Installer Encore dans les applications Symfony

```composer require symfony/webpack-encore-bundle```

```yarn install```

#### Installer Encore dans les applications non Symfony

```yarn add @symfony/webpack-encore --dev```

## Installation du SCSS dans ton projet

Décocher la suivante dans le fichier __webpack.config.js__

```.enableSassLoader()```
