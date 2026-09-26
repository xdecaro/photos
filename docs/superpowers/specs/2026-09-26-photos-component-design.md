# com_xdecarophotos — specifica architetturale

Data: 2026-09-26
Stato: proposta da approvare prima dell'implementazione
Target: Joomla 6
Componente: `com_xdecarophotos`
Pacchetto: `pkg_xdecarophotos`
Namespace PHP: `xdecaro\Component\Photos`
Core di riferimento: `pkg_core` 2.1.0

## 1. Obiettivo

`com_xdecarophotos` è il componente centrale dell'ecosistema xdecaro per la gestione delle immagini. Deve servire tutti i componenti xdecaro senza assorbirne la logica di dominio.

Il componente deve coprire due aree principali:

1. **Photo Studio** — acquisizione, scatto diretto, ritaglio, centratura e generazione di varianti come fototessera, avatar, tessera e figurina.
2. **Gallery** — consultazione e gestione delle immagini in griglia o elenco, con filtri per proprietario, tipo, contesto e stato.

Il componente deve essere riutilizzabile da People, Membership, Organizations, Competitions e da futuri prodotti xdecaro.

## 2. Principi architetturali

### 2.1 Photos possiede le immagini

`com_xdecarophotos` possiede:

- metadati delle immagini;
- file originali;
- varianti generate;
- ritagli e trasformazioni;
- tipi di foto;
- stato principale/non principale;
- contesto opzionale;
- logica Photo Studio e Gallery.

Gli altri componenti non devono duplicare questa logica.

### 2.2 Core resta piccolo e domain-neutral

Photos dipende da Core, non il contrario.

Core fornisce:

- `xdecaro\Core\Integration\EntityReference`;
- `xdecaro\Core\Integration\RelationReference` quando utile;
- `xdecaro\Core\Asset\AssetService`;
- design token e primitive UI condivise;
- contratti di integrazione domain-neutral.

Core non deve conoscere crop, fototessere, gallery, figurine o file fotografici.

### 2.3 Nessun UID globale aggiuntivo

Photos non introduce un UID globale centrale.

I collegamenti verso entità esterne usano il contratto Core `EntityReference`:

- `component`;
- `entity`;
- `id`.

Esempio proprietario:

```text
com_xdecaropeople / person / 125
```

Esempio contesto:

```text
com_xdecarocompetitions / competition / 32
```

Photos non deve leggere direttamente le tabelle private degli altri componenti.

## 3. Integrazioni previste

### People

Usi principali:

- foto principale persona;
- avatar;
- fototessera;
- archivio storico delle foto della persona.

Riferimento:

```text
component = com_xdecaropeople
entity    = person
id        = <id persona>
```

### Membership

Usi principali:

- foto tessera;
- fototessera per pratica;
- eventuale snapshot fotografico riferito a un determinato record di tesseramento.

La proprietà della foto resta preferibilmente riferita alla persona quando la foto rappresenta la persona; il record Membership può essere usato come contesto quando serve distinguere una specifica pratica/stagione.

### Organizations

Usi principali:

- logo;
- immagine principale;
- gallery organizzazione;
- eventuali immagini storiche.

### Competitions

Usi principali:

- foto atleta;
- figurina;
- foto accredito;
- foto roster;
- foto squadra/club;
- gallery competizione;
- immagini riferite a una specifica stagione o edizione.

### Futuri componenti

Un nuovo componente xdecaro deve potersi integrare senza modifica strutturale di Photos purché possa fornire un `EntityReference` valido.

## 4. Modello dei dati

La prima versione deve separare almeno:

1. **foto**;
2. **varianti**;
3. **collegamenti/proprietà**;
4. **contesto opzionale**.

### 4.1 Tabella principale proposta

`#__xdecarophotos_photos`

Campi concettuali:

- `id` — PK numerica locale;
- `owner_component` — es. `com_xdecaropeople`;
- `owner_entity` — es. `person`;
- `owner_id` — identificatore stabile serializzato come stringa;
- `context_component` — nullable;
- `context_entity` — nullable;
- `context_id` — nullable;
- `photo_type` — tipo semantico;
- `title` — titolo opzionale;
- `description` — descrizione opzionale;
- `original_path` — file originale gestito da Photos;
- `mime_type`;
- `width`;
- `height`;
- `file_size`;
- `orientation`;
- `is_primary`;
- `status`;
- `season` — nullable, solo quando utile come metadato esplicito;
- `created`;
- `created_by`;
- `modified`;
- `modified_by`.

Vincoli:

- owner obbligatorio per una foto collegata a un'entità;
- context interamente nullo oppure completo nei tre campi;
- un solo record `is_primary = 1` per la stessa combinazione owner + tipo + contesto, salvo casi esplicitamente definiti;
- validazione dei riferimenti con `EntityReference` prima del salvataggio.

### 4.2 Varianti

`#__xdecarophotos_variants`

Campi concettuali:

- `id`;
- `photo_id`;
- `preset`;
- `path`;
- `mime_type`;
- `width`;
- `height`;
- `crop_x`;
- `crop_y`;
- `crop_width`;
- `crop_height`;
- `rotation`;
- `generated`;
- `checksum` opzionale.

La foto originale non deve essere distrutta quando viene creato un crop.

## 5. Tipi di foto iniziali

La versione iniziale prevede i seguenti tipi semantici:

- `original`;
- `avatar`;
- `passport`;
- `card`;
- `sticker`;
- `logo`;
- `gallery`;
- `thumbnail` come variante tecnica, non necessariamente come tipo scelto dall'utente.

I tipi devono essere estendibili senza migrazioni distruttive.

## 6. Preset

I preset definiscono il formato di uscita, non il proprietario della foto.

Preset iniziali:

- **Avatar** — quadrato 1:1;
- **Fototessera** — verticale, con area viso guidata;
- **Tessera** — variante adatta all'uso su card/tessere;
- **Figurina** — verticale, pensata per presentazione atleta;
- **Logo** — preserva trasparenza quando disponibile;
- **Thumbnail** — anteprima ottimizzata.

Le dimensioni fisiche ufficiali di eventuali documenti/tessere non devono essere inventate nel codice. I preset tecnici devono poter essere configurati o definiti in seguito in base al requisito reale del prodotto consumatore.

## 7. Photo Studio

Flusso raccomandato:

1. selezione dell'entità proprietaria;
2. scelta tra upload e fotocamera;
3. acquisizione immagine;
4. correzione orientamento;
5. rilevamento volto quando disponibile;
6. proposta di centratura automatica;
7. anteprima;
8. correzione manuale con trascinamento/zoom/rotazione;
9. scelta preset/uso;
10. salvataggio originale;
11. generazione variante;
12. eventuale impostazione come foto principale.

La centratura automatica non deve salvare senza anteprima e possibilità di correzione manuale.

### 7.1 Fotocamera

Su browser compatibili si usa l'API nativa `getUserMedia`.

Requisiti:

- nessuna fotocamera obbligatoria;
- upload sempre disponibile come fallback;
- selezione camera anteriore/posteriore quando il browser lo permette;
- consenso esplicito del browser;
- nessun invio a servizi cloud esterni per lo scatto.

### 7.2 Rilevamento volto

Obiettivo: aiutare il crop, non identificare la persona.

Il riconoscimento biometrico dell'identità non rientra nella prima versione.

La prima versione deve preferire elaborazione locale/browser quando tecnicamente affidabile. Se non disponibile, il sistema continua con crop manuale.

## 8. Gallery

La Gallery è una vista di Photos, non un componente separato.

Modalità:

- griglia;
- elenco amministrativo.

Filtri iniziali:

- componente proprietario;
- tipo entità;
- ID/riferimento proprietario;
- tipo foto;
- contesto;
- stagione;
- stato;
- principale/non principale.

Funzioni:

- ricerca;
- anteprima;
- modifica metadati;
- modifica crop;
- impostazione come principale;
- visualizzazione varianti;
- eliminazione controllata;
- caricamento nuova foto.

Su mobile la griglia deve essere prioritaria e non richiedere scorrimento orizzontale per le operazioni normali.

## 9. Storage

Regole:

- nessun consumer deve derivare il percorso file dagli ID;
- il percorso fisico è responsabilità esclusiva di Photos;
- nomi file non devono contenere dati personali inutili;
- originali e varianti devono essere separati logicamente;
- cancellare una variante non deve cancellare l'originale;
- cancellare un originale deve seguire un flusso controllato che consideri le varianti dipendenti.

Struttura indicativa, non API pubblica:

```text
/images/xdecaro/photos/
  originals/
  variants/
```

La struttura interna può cambiare senza rompere i componenti consumer perché essi non devono usarla direttamente.

## 10. API pubblica del componente

Photos deve esporre un servizio pubblico stabile, invece di richiedere query alle proprie tabelle.

Interfaccia concettuale iniziale:

```php
getPrimaryPhoto(EntityReference $owner, string $type, ?EntityReference $context = null)
getPhotos(EntityReference $owner, array $filters = [])
getPhoto(int $photoId)
createPhoto(EntityReference $owner, array $payload)
setPrimaryPhoto(int $photoId)
createVariant(int $photoId, string $preset, array $options = [])
```

Le firme definitive saranno fissate in implementazione con copertura test, ma il confine pubblico deve seguire questa responsabilità.

Nessun consumer deve accedere a `#__xdecarophotos_*` direttamente.

## 11. UI condivisa con Core

Photos usa `xdecaro\Core\Asset\AssetService`.

Per le primitive generiche:

- `.xdecaro-scope`;
- `.xdecaro-card`;
- `.xdecaro-toolbar`;
- `.xdecaro-button`;
- `.xdecaro-badge`;
- `.xdecaro-field`;
- `.xdecaro-table`;
- `.xdecaro-empty`;
- `.xdecaro-loader`;
- `.xdecaro-modal`.

Photos mantiene CSS/JS locale solo per comportamento specifico:

- crop canvas;
- overlay volto;
- zoom/drag;
- fotocamera;
- gallery responsive;
- preview immagini.

Non si devono duplicare in Photos pulsanti, card, form o badge già offerti da Core.

## 12. Dipendenza da Core

Per la prima versione, Core è una dipendenza dichiarata del pacchetto Photos.

Versione minima proposta: **Core 2.1.0**.

Motivazione:

- Photos nasce nuovo e non deve portarsi dietro compatibilità legacy;
- deve usare namespace canonicale lowercase `xdecaro\Core`;
- deve usare direttamente `EntityReference` e `AssetService` verificati nel pacchetto 2.1.0 fornito.

L'installer deve mostrare un errore chiaro se Core richiesto non è disponibile o è troppo vecchio.

## 13. Sicurezza e privacy

Photos gestisce immagini potenzialmente personali.

Requisiti minimi:

- ACL Joomla su visualizzazione, creazione, modifica, eliminazione e amministrazione;
- token CSRF sulle operazioni di scrittura;
- validazione MIME reale e non solo estensione file;
- limiti configurabili di dimensione;
- rifiuto di formati non supportati;
- nomi file generati dal sistema;
- protezione da path traversal;
- stripping o gestione consapevole dei metadati EXIF nelle varianti pubbliche;
- nessun dato biometrico persistente per il rilevamento volto;
- nessun riconoscimento dell'identità nella v1;
- controllo autorizzazione anche quando il consumer conosce il `photo_id`.

## 14. Eliminazione e integrità

Una foto può essere collegata a dati storici importanti.

La v1 deve distinguere:

- disattivazione/non pubblicazione;
- eliminazione definitiva.

L'eliminazione definitiva deve essere protetta da conferma e verificare le dipendenze interne delle varianti.

Photos non deve cancellare entità negli altri componenti.

Se l'entità proprietaria non esiste più, Photos può evidenziare il riferimento come non risolvibile senza assumere autonomamente che il file vada cancellato.

## 15. Diagnostica

Il backend deve mostrare almeno:

- versione componente;
- versione pacchetto;
- Core installato e versione rilevata;
- disponibilità `EntityReference`;
- disponibilità `AssetService`;
- directory storage scrivibili;
- formati immagine supportati dal runtime PHP;
- limite upload PHP rilevato;
- eventuali capability opzionali del browser non trattate come errori server.

Non deve mostrare dati personali nelle schermate diagnostiche.

## 16. Backend amministrativo iniziale

Menu proposto:

- Dashboard;
- Foto;
- Gallery;
- Preset;
- Diagnostica;
- Opzioni.

`Photo Studio` può essere un flusso/azione aperto da `+ Nuova foto`, non necessariamente una voce menu autonoma nella prima versione.

### Dashboard

Indicatori utili:

- totale foto;
- originali;
- varianti;
- foto senza proprietario valido/riferimento non risolvibile;
- spazio occupato;
- ultime foto.

Nessuna metrica deve diventare obbligatoria se rende la prima release inutilmente complessa.

## 17. Frontend

La v1 deve progettare i servizi in modo compatibile con il frontend, ma il frontend pubblico completo può essere incrementale.

Prima esigenza:

- permettere ai consumer di ottenere l'immagine corretta tramite API pubblica;
- supportare viste o layout riusabili per avatar, foto profilo, logo e gallery quando servono.

Nessun consumer deve copiare il motore Photo Studio dentro il proprio componente.

## 18. Accessibilità e responsive

Requisiti:

- controlli usabili da tastiera;
- label testuali e non solo icone;
- focus visibile;
- alternative testuali per immagini dove semanticamente necessarie;
- crop utilizzabile anche senza drag preciso tramite controlli accessibili;
- interfaccia mobile senza overflow orizzontale nelle funzioni principali;
- target touch adeguati;
- rispetto di `prefers-reduced-motion` per animazioni non essenziali.

## 19. Compatibilità

Target dichiarato: **Joomla 6**.

Non verrà aggiunta compatibilità Joomla 4/5 salvo richiesta esplicita e verifica reale.

PHP minimo deve seguire il requisito effettivo della versione Joomla 6 supportata e sarà fissato nel manifest dopo verifica durante l'implementazione.

## 20. Packaging e versioning

Prima release proposta:

- componente `com_xdecarophotos`;
- pacchetto installabile `pkg_xdecarophotos`;
- versione iniziale `0.1.0` durante sviluppo oppure `1.0.0` solo quando il set minimo è realmente stabile.

Ogni ZIP distribuito deve avere metadata/versione coerenti e non devono esistere file diversi con lo stesso numero di versione pubblicato.

## 21. Test minimi obbligatori

### Unit/integration

- validazione owner `EntityReference`;
- validazione context `EntityReference`;
- creazione foto;
- selezione primary;
- creazione variante;
- integrità originale/varianti;
- ACL;
- CSRF per controller di scrittura;
- MIME e upload invalidi;
- assenza di accesso diretto a tabelle consumer.

### Smoke Joomla

- installazione su Joomla 6;
- rimozione pulita;
- aggiornamento pacchetto;
- Core 2.1.0 rilevato;
- AssetService caricato;
- backend Foto apre senza errori;
- upload immagine;
- generazione almeno di una variante;
- layout desktop e mobile;
- light/dark con Core UI.

### Browser

- upload desktop;
- upload mobile;
- camera quando disponibile;
- fallback quando `getUserMedia` non è disponibile/negato;
- crop manuale;
- nessun blocco se il face detection non è disponibile.

## 22. Scope della prima milestone

La prima milestone deve arrivare a un prodotto piccolo ma realmente utilizzabile:

1. package Joomla 6 installabile;
2. dipendenza Core 2.1.0;
3. tabella foto e varianti;
4. upload sicuro;
5. owner tramite `EntityReference`;
6. context opzionale tramite `EntityReference`;
7. Photo Studio con crop manuale;
8. scatto camera con fallback;
9. preset Avatar e Fototessera;
10. Gallery backend;
11. foto principale;
12. API pubblica minima per i consumer;
13. UI Core condivisa;
14. diagnostica;
15. test automatici essenziali.

La generazione avanzata di figurine, template grafici complessi, riconoscimento identità, rimozione automatica sfondo, album pubblici avanzati e moderazione massiva restano fuori dalla prima milestone.

## 23. Criteri di successo

La milestone è riuscita quando:

- una persona di People può essere riferita da Photos senza query alle tabelle People;
- si può caricare o scattare una foto;
- si può correggere il crop;
- l'originale resta conservato;
- si può produrre una variante Fototessera o Avatar;
- si può impostare una foto principale;
- un consumer può recuperarla tramite un servizio pubblico Photos;
- Gallery consente di ritrovare e gestire le immagini;
- l'interfaccia appare coerente con Core 2.1.0;
- il flusso funziona su desktop e mobile;
- l'assenza di funzioni browser opzionali non rompe il componente.

## 24. Decisioni fissate

- Nome tecnico: `com_xdecarophotos`.
- Nome vendor sempre lowercase: `xdecaro`.
- Un solo componente per Photo Studio e Gallery.
- Photos è il proprietario della logica immagini.
- Core non diventa un archivio foto.
- Nessun nuovo UID globale centrale.
- Integrazione tramite `EntityReference`.
- Design condiviso tramite `AssetService` Core.
- Target Joomla 6.
- Core 2.1.0 come baseline iniziale.
- Originale sempre conservato; crop e formati sono varianti.
- Face detection solo come assistenza al crop, non riconoscimento identità.
