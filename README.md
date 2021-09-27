# Introdução #

<p>
    Esse repositório se refere a uma dependência à ser inserida nos projetos onde exista a necessidade de protejer senhas/chaves/tokens e demais informações sensíveis.
</p>

<br>

# Como configurar

### 1º Passo 
Adicione a seguinte variavel ao seu ambiente (.env): 
    
    GOOGLE_APPLICATION_CREDENTIALS=/home/$USER/.service-accounts/secret-manager-account.json

onde secret-manager-account.json é uma conta de serviço na GCP que deve possuir ao menos as seguintes permissões:
    
`secretmanager.secrets.create`

`secretmanager.secrets.get`

`secretmanager.versions.access`

`secretmanager.versions.add`

`secretmanager.versions.list`

sugerido utilizar o papel: 

`Administrador do Gerenciador de secrets`

<hr>

### 2º Passo

Onde precisar utilizá-la, importe a dependência com:

    <?php
    use Rubeus\SecretManagerGcp\SecretManager;




