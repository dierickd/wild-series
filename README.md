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

![Guide installation Bootstrap (Symfony)](https://symfony.com/doc/current/frontend/encore/bootstrap.html)

#### Bootstrap CSS

```yarn add bootstrap --dev```

Importer Bootstrap dans le fichier scss

```@import "~bootstrap/scss/bootstrap";```

#### Bootstrap JS

```yarn add jquery popper.js --dev```

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

```yarn encore dev --watch```
