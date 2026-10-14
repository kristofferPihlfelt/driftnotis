# Driftnotis

Gratis plugin från [wordpressutveckling.se](https://wordpressutveckling.se/). Koden förklaras rad för rad i guiden: **[https://wordpressutveckling.se/bygg-ditt-forsta-wordpress-plugin/](https://wordpressutveckling.se/bygg-ditt-forsta-wordpress-plugin/)**

Byggt av [Kristoffer Pihlfelt](https://pihlfelt.se) på [Pihlfelt & Vänner](https://pihlfelt.se). Licens: GPL-2.0-or-later.

- **Bidragsgivare:** Pihlfelt & Vänner
- **Kräver WordPress:** 6.7
- **Kräver PHP:** 8.2
- **Version:** 1.0.0
- **Licens:** GPL-2.0-or-later

## Vad det gör

Visar en meddelanderad högst upp på varje sida, till exempel "Vi har stängt för inventering 24 december". Text, på/av och bakgrundsfärg ställs in under Inställningar > Driftnotis. Raden skrivs ut på wp_body_open, så temat måste anropa wp_body_open() (alla blockteman och nyare klassiska teman gör det). Inställningen tas bort när pluginet avinstalleras.

## Installation

1. Gå till Tillägg > Lägg till nytt > Ladda upp tillägg och välj zip-filen.
2. Aktivera Driftnotis.
3. Gå till Inställningar > Driftnotis, skriv texten, bocka i Visa raden och spara.

## Källa

Koden förklaras rad för rad i guiden "Bygg ditt första WordPress-plugin från grunden":
https://wordpressutveckling.se/bygg-ditt-forsta-wordpress-plugin/

Den senaste versionen finns alltid på samma adress:
https://wordpressutveckling.se/ladda-ner/driftnotis/

## Ändringslogg

### 1.0.0
* Första versionen, samma kod som i guiden.
