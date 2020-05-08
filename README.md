# wild-series

## Créer nouveau projet Symfony

```terminal
symfony new --full <name_project> --version=lts
```

## Installation de Webpack

![Documentation Webpack/Symfony](https://symfony.com/doc/current/frontend/encore/installation.html)

#### Installer Encore dans les applications Symfony

```terminal
composer require symfony/webpack-encore-bundle
```

```terminal
yarn install
```

#### Installer Encore dans les applications non Symfony

```terminal
yarn add @symfony/webpack-encore --dev
```

## Installation du SCSS dans ton projet

Décoche la suivante dans le fichier _webpack.config.js_

```javascript
.enableSassLoader()
```

## installation de Bootstrap

![Guide installation Bootstrap (Symfony)](https://symfony.com/doc/current/frontend/encore/bootstrap.html)

#### Bootstrap CSS

```terminal
yarn add bootstrap --dev
```

Importe Bootstrap dans le fichier scss

```SCSS
@import "~bootstrap/scss/bootstrap";
```

#### Bootstrap JS

```terminal
yarn add jquery popper.js --dev
```

Dans le fichier Javascript

```javascript
// app.js

const $ = require('jquery');
// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
require('bootstrap');

// or you can include specific pieces
// require('bootstrap/js/dist/tooltip');
// require('bootstrap/js/dist/popover');

$(document).ready(function() {
    $('[data-toggle="popover"]').popover();
});

```

## Relancer le _build_ Webpack

```terminal
yarn encore dev --watch
```
