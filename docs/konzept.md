# Compatibility Fix for Safe SVG – Konzept und Aufbau

Version: 1.0 · Stand: 03.10.2026 · Plugin-Version: 1.1.3

## 1. Zweck

Das Plugin behebt einen Konflikt zwischen „Safe SVG" (10up) und „Enable
Media Replace": Ohne es lassen sich vorhandene Medien nicht durch SVG-Dateien
ersetzen. Es registriert die nötigen MIME-Filter global, aber nur, wenn
Safe SVG aktiv ist, damit kein ungeprüfter SVG-Upload möglich wird. Eine
einzige Datei, keine eigenen Strings, keine Optionen. Seit 1.0.0 im
WordPress-Verzeichnis (`https://wordpress.org/plugins/compatibility-fix-for-safe-svg/`).

## 2. Verzeichnisse

Das Projekt liegt unter
`~/dev/jgorres-im-WP-Repository/compatibility-fix-for-safe-svg/` nach dem
Schema der `README.md` in `~/dev/jgorres-im-WP-Repository/`. Das Git-Repo
entstand am 03.10.2026 aus dem SVN-trunk; ältere Stände gibt es nur als
SVN-Tags.

```
compatibility-fix-for-safe-svg/
├── plugin/                    ausgeliefertes Plugin
│   ├── compatibility-fix-for-safe-svg.php   Header und Filter
│   ├── readme.txt             für das WordPress-Verzeichnis
│   └── languages/index.php    keine .pot nötig (keine eigenen Strings)
├── assets/                    WP.org-Assets: icon.svg, icon-128x128, icon-256x256,
│                              banner-772x250, banner-1544x500
├── docs/                      diese Doku
├── glotpress/                 .po-Stände von translate.wordpress.org: Header (du)
│                              und Readme (du und Sie)
├── svn/                       SVN-Checkout trunk/, tags/1.1.1, tags/1.1.2, assets/ (nicht im Git)
├── dist/                      Release-ZIPs (nicht im Git)
├── build.sh                   prüft Header und Stable tag, schreibt dist/<slug>-<version>.zip
├── .distignore, LICENSE, composer.json, phpcs.xml.dist, phpstan.neon.dist,
│   phpstan-bootstrap.php, stubs/
```

Git-Remote `origin`: `https://github.com/jgorres/compatibility-fix-for-safe-svg-plugin`
(öffentlich, Branch `main`; GitHub hat den Initial-Commit mit `LICENSE`
angelegt); diese Adresse steht als `Plugin URI` im Header. Push von Hand nach
jedem Commit.

Namensregeln: Slug und Text-Domain `compatibility-fix-for-safe-svg`,
Funktionen `compatibility_fix_for_safe_svg_` (seit 1.0.0, bleibt wegen der
veröffentlichten Hook-Namen; kein `jgor_`-Prefix).

## 3. Werkzeuge

```bash
composer install       # einmalig
composer check         # PHPCS (WPCS, PHP 7.4+) und PHPStan Level 6
./build.sh             # Release-ZIP nach dist/
```

Plugin Check auf dem entpackten ZIP:
`wp plugin check <entpackt> --path=/var/www/local-sites/plugintest.local`.

## 4. Release

1. Version in Header und `readme.txt` (Stable tag, Changelog).
2. `composer check`, `./build.sh`, Plugin Check.
3. `plugin/` nach `svn/trunk/` spiegeln, `svn cp trunk tags/<version>`, `svn ci`.
4. Bei Readme-Änderungen die `.po` in `glotpress/` nachziehen und über
   translate.wordpress.org einreichen (Skill `glotpress-de`).

## 5. Entschiedene Fragen

| Frage | Entscheidung | Grund |
| --- | --- | --- |
| Übersetzungen | nur über translate.wordpress.org | das Plugin hat keine eigenen Strings; Header und Readme übersetzt GlotPress |
| `Tested up to` | nur in `readme.txt` | die Einreichung lehnt die Zeile im Header ab; aus dem trunk am 01.10.2026 entfernt, Release mit 1.1.3 |
| `Plugin URI` | GitHub-Repo | WordPress.org verlangt eine eigene Seite, keine WordPress.org-Adresse |
| Funktions-Prefix | `compatibility_fix_for_safe_svg_` | veröffentlicht seit 1.0.0, Umbenennung brächte nichts |

## Änderungen

| Version | Datum | Änderung |
| --- | --- | --- |
| 1.0 | 03.10.2026 | Erste Fassung: Git-Repo aus dem SVN-trunk im Ordnerschema, Plugin 1.1.3 (`Plugin URI`, Header ohne `Tested up to`), Prüfwerkzeuge, `build.sh` |
