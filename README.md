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

Décocher la suivante dans le fichier _webpack.config.js_

```.enableSassLoader()```

## installation de Bootstrap

![Guide installation Bootstrap _(Symfony)_](https://symfony.com/doc/current/frontend/encore/bootstrap.html)


## Relancer le _build_ Webpack

```yarn encore dev --watch```
