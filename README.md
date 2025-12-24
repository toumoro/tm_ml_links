# Liens étendus Toumoro (tm_ml_links)
ENGLISH FOLLOW BELOW

Cette extension TYPO3 améliore le rendu standard des liens en fournissant des icônes et des informations supplémentaires pour différents types de liens. C'est une réécriture moderne de l'extension `ml_links`, compatible avec TYPO3 v13.4.

## Fonctionnalités

*   **Liens améliorés par des icônes :** Ajoute automatiquement des icônes aux liens en fonction de leur type.
*   **Liens de fichiers :** Affiche des icônes pour une large gamme de types de fichiers (par ex., PDF, DOC, ZIP, JPG, PNG). Elle peut également afficher la taille du fichier.
*   **Liens externes :** Ajoute une icône de globe aux liens externes. Des icônes spéciales peuvent être configurées pour des domaines spécifiques (par ex., Wikipedia, TYPO3.org).
*   **Liens Mailto :** Ajoute une icône de courrier aux liens e-mail.
*   **Liens internes :** Peuvent être stylisés différemment des liens externes.
*   **Hautement configurable :** L'extension peut être entièrement configurée via TypoScript pour personnaliser l'apparence et le comportement des liens.

## Installation

1.  Installez l'extension via composer :
    ```bash
    composer require toumoro/tm-ml-links
    ```
2.  Incluez le TypoScript statique « Default TS » de l'extension dans le gabarit de votre site.

## Configuration

L'extension est configurée via TypoScript. Vous pouvez modifier la configuration dans `plugin.tx_tmmllinks` pour changer le comportement.

### Exemple de Configuration

Voici un extrait de la configuration TypoScript par défaut, qui définit comment les liens externes et les fichiers PDF sont gérés :

```typoscript
plugin.tx_tmmllinks {
    # Séparateur entre l'icône et le texte du lien
    separator = &nbsp;

    # Définition pour les liens externes
    external {
        10.image = EXT:tm_ml_links/Resources/Public/Icons/globe.gif
        10.image.link = 0
        20.linkTag = 1
    }

    # Définition pour les fichiers PDF
    pdf {
        10.image = EXT:tm_ml_links/Resources/Public/Icons/filetypes/pdf.png
        10.image.link = 0
        20.linkTag = 1
        30.filesize = 1
    }

    # Configuration spéciale pour les liens Wikipedia
    externalDomain {
        1 {
            domain = http://en.wikipedia.org
            10.image = EXT:tm_ml_links/Resources/Public/Icons/domains/wikipedia.png
            10.image.link = 0
            20.linkTag = 1
        }
    }
}
```

Vous pouvez trouver toutes les options disponibles dans le fichier `setup.typoscript`.

## Auteur

Cette extension est créée par Toumoro.

## Licence

La licence de cette extension n'est pas spécifiée dans les fichiers fournis. Veuillez contacter l'auteur pour les informations de licence.

----------------------------------------------------------------------------------------


# Toumoro Extended Links (tm_ml_links)

This TYPO3 extension enhances the standard link rendering by providing icons and additional information for various link types. It is a modern rewrite of the `ml_links` extension, compatible with TYPO3 v13.4.

## Features

*   **Icon-enhanced links:** Automatically adds icons to links based on their type.
*   **File links:** Displays icons for a wide range of file types (e.g., PDF, DOC, ZIP, JPG, PNG). It can also show the file size.
*   **External links:** Adds a globe icon to external links. Special icons can be configured for specific domains (e.g., Wikipedia, TYPO3.org).
*   **Mailto links:** Adds a mail icon to email links.
*   **Internal links:** Can be styled differently from external links.
*   **Highly configurable:** The extension can be fully configured through TypoScript to customize the appearance and behavior of links.

## Installation

1.  Install the extension via composer:
    ```bash
    composer require toumoro/tm-ml-links
    ```
2.  Include the static TypoScript "Default TS" from the extension in your site's template.

## Configuration

The extension is configured via TypoScript. You can modify the setup in `plugin.tx_tmmllinks` to change the behavior.

### Example Configuration

Here is a snippet from the default TypoScript setup, which defines how external links and PDF files are handled:

```typoscript
plugin.tx_tmmllinks {
    # Separator between icon and link text
    separator = &nbsp;

    # Definition for external links
    external {
        10.image = EXT:tm_ml_links/Resources/Public/Icons/globe.gif
        10.image.link = 0
        20.linkTag = 1
    }

    # Definition for PDF files
    pdf {
        10.image = EXT:tm_ml_links/Resources/Public/Icons/filetypes/pdf.png
        10.image.link = 0
        20.linkTag = 1
        30.filesize = 1
    }

    # Special configuration for Wikipedia links
    externalDomain {
        1 {
            domain = http://en.wikipedia.org
            10.image = EXT:tm_ml_links/Resources/Public/Icons/domains/wikipedia.png
            10.image.link = 0
            20.linkTag = 1
        }
    }
}
```

You can find all the available options in the `setup.typoscript` file.

## Author

This extension is authored by Toumoro.

## License

The license for this extension is not specified in the provided files. Please contact the author for licensing information.
