# CHAT_APP

----------------------------------------------------
### Como o projetos esta dividido?

O projeto esta divido em 3 partes:
- Ambiente docker
- Ambiente PHP
- Integração com o sistema Swoole
----------------------------------------------------
### Estrutura do Ambiente DOCKER
``` sh
# https://hub.docker.com/u/facchin

.
└── facchin/devbase-base:ubuntu-16.04
    └── facchin/devbase-supervisor:ubuntu-16.04
        └── facchin/devbase-bootstrap:ubuntu-16.04
   

3 imagens uma dependente da outra, sendo a imagem 'devbase-base' utilizada
pra o Ambiente PHP
```
----------------------------------------------------
### SETUP DO PROJETO

----------------------------------------------------
### Estrutura de arquivos principais do projeto corrente

``` sh
# Host system
$ tree
.
├── app
│   ├── Core
│   │   ├── Db.php
│   │   └── Model.php
│   ├── Models
│   │   ├── Message.php
│   │   ├── Room.php
│   │   └── Session.php
│   └── WebsocketServer.php
├── composer.json
├── der
│   ├── app.mwb
│   └── app.mwb.bak
├── public
│   ├── index.php
│   ├── js
│   │   ├── comp_chat.js
│   │   ├── comp_chat_suport.js
│   └── views
│       ├── atendente.html
│       ├── chat.html
│       ├── home.html
│       └── suport.html
├── README.md
└── server.php

```
----------------------------------------------------
### Detalhes do Ambiente DOCKER
- Todos os códigos fontes estão disponiveis em: https://github.com/patrickfacchin/devbase
- Após os 'push' no git, as imagens no hub.docker são geradas automaticamente
----------------------------------------------------

