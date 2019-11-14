# app
app
----------------------------------------------------
Como o projetos esta dividido?

O projeto esta divido em 3 partes:
- Ambiente docker
- Ambiente PHP
# TODO
- Sistema de chat escalável

----------------------------------------------------
### Estrutura do Ambiente DOCKER
``` sh
# https://hub.docker.com/u/facchin

.
├── facchin/devbase-base:ubuntu-16.04
    └── facchin/devbase-supervisor:ubuntu-16.04
        └── facchin/devbase-bootstrap:ubuntu-16.04
   

3 imagens uma dependente da outra, sendo a imagem 'devbase-base' utilizada
pra o Ambiente PHP
```
----------------------------------------------------
### Estrutura base do Ambiente PHP

``` sh
# Host system
$ tree
.
├── docker
│   └── nginx.conf
└── public
    └── index.php

2 directories, 2 files
```
----------------------------------------------------
### Detalhes do Ambiente DOCKER
- Todos os códigos fontes estão disponiveis em: https://github.com/patrickfacchin/devbase
- Após os 'push' no git, as imagens no hub.docker são geradas automaticamente
----------------------------------------------------
### Detalhes do Ambiente PHP
#### Obs.: é necessário aguardar algumas minuto (4min) para que todas as configurações terminem.
- Em cima do ambiente PHP foi feito em PhalconPHP uma estrutura base de um 
projeto web em HMVC
- O 'styleguide' do projeto se encontra em .editorconfig
----------------------------------------------------
