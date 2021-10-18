# Introdução #

<p>
    Esse repositório se refere a uma dependência à ser inserida nos projetos onde exista a necessidade de protejer senhas/chaves/tokens e demais informações sensíveis.
</p>

<br>

# Como utilizar o Cipher

<p>Cipher é uma classe que disponibiliza as funções 'encrypt' e 'decrypt', cujos nomes remetem obviamente aos seus respectivos propósitos. As funções de Cipher devem ser utilizadas para aqueles valores cujo conteúdo precisa ser protegido e posteriormente recuperado em seu formato original.</p>

## 1º Passo

Essa classe exige que você armazene de forma segura duas chaves (devido ao uso de openssl_encrypt e openssl_decrypt) para criptografar e descriptografar uma string. Recomendamos configurá-las através de variáveis de ambiente.

No seu .env, adicione:

    MASTER_SECRET_KEY=senhaExemplo
    MASTER_SECRET_IV=senhaForteExemplo

## 2º Passo

Onde precisar utilizar, importe a dependência com:

    <?php
    use Rubeus\SecretManagerGcp\Cipher;


## Exemplos de uso

### Para proteger uma string
    
    Cipher::encrypt(ENV(MASTER_SECRET_KEY), ENV(MASTER_SECRET_IV), "string_for_token_api_client");
    
`saída esperada:`

    string(60) "VExSWWVHTlBGOXpDbC90c2JNTVhEcEV5eXBBSDdrclRLMGFVWUtwY25Ncz0="

<br>

### Para recuperar o valor original de uma string protegida

    Cipher::decrypt(ENV(MASTER_SECRET_KEY), ENV(MASTER_SECRET_IV), "dlE0VUZ4aUVMbDF3dTlOYmdtVG5UUT09");

`saída esperada:`

    string(27) "string_for_token_api_client"

<br>

# Como utilizar o Hash

<p>O uso do Hash deve ser considerado para aqueles valores que - desde o momento de sua criação - não precisarão nunca mais serem lidos na sua forma original. Por exemplo, senhas de usuários para autenticação no próprio sistema.</p>

## 1º Passo

<p>Apenas é necessário importar a classe onde for necessária sua utilização</p>
    
    <?php
    use Rubeus\SecretManagerGcp\Hash;

<br>

## Exemplos de uso

Para criar o hash de uma senha por exemplo, basta chamar a seguinte função

    $secret = Hash::create("senhaUsuario");

`saída esperada:`

    string(96) "$argon2i$v=19$m=32768,t=4,p=2$a0xRMEUvZTJPd0lvZ2tSdg$wchL2gWLMmqpSt+W5nLCE8xJ6CLaZ1XrTdMBe/RBJZ0"

<br>

Existem parâmetros opcionais que você pode passar para a função <u>create()</u>, que se referem ao custo computacional para criação do hash, exemplo:

    $memoryCost = 1<<15;
    $timeCost = 4;
    $threads = 2;
    $secret = Hash::create("senhaUsuario", $memoryCost, $timeCost, $threads);

`saída esperada:`

    string(96) "$argon2i$v=19$m=32768,t=4,p=2$a0xRMEUvZTJPd0lvZ2tSdg$wchL2gWLMmqpSt+W5nLCE8xJ6CLaZ1XrTdMBe/RBJZ0"

*  Dica: Tenha em mente que aumentar o custo computacional irá deixar a função mais lenta, assim como, diminuí-lo irá deixá-la mais rápida. Altere de acordo com o custo/benefício que deseja assumir para a aplicação que estiver trabalhando.

<br>

Para validar/verificar um hash

    $verify = Hash::verify("senhaUsuario", '$argon2i$v=19$m=262144,t=4,p=2$RDkxWlNjMW1aa0FPQWg1bQ$rVZMukB5qPTo6JSxpnxp/Bd18sdG//1IEuGFktmRkrs');

`saída esperada:`

    bool(true)

<br>

Para tratar o resultado como um objeto, você pode usar a função <u>toObject()</u>

    $secretAsObject = Hash::toObject('$argon2i$v=19$m=262144,t=4,p=2$RDkxWlNjMW1aa0FPQWg1bQ$rVZMukB5qPTo6JSxpnxp/Bd18sdG//1IEuGFktmRkrs');

`saída esperada:`
    
    object(stdClass)#1 (7) {
        ["algorithm"]=> string(7) "argon2i"
        ["version"]=> string(4) "v=19"
        ["memory_cost"]=> string(7) "m=32768"
        ["time_cost"]=> string(3) "t=4"
        ["parallelism"]=> string(3) "p=2"
        ["salt"]=> string(22) "RVpFT0UzaExYZkFNZFhhaQ"
        ["secret"]=> string(43) "P63kY29W3vL0mauPA4g0t+EkTtiiA1j9E3MOe2kdPAQ"
    }

<br>

Para realizar o oposto e transformar o objeto do exemplo anterior novamente em string, utilize a função <u>toString()</u>

    $secret = Hash::toString($secretAsObject);

`saída esperada:`

    string(96) "$argon2i$v=19$m=32768,t=4,p=2$SGx2MkprY1FvOFFFQW1YTg$be782SmBNjc5q+DYZHfyTRidu/Pq/vGuxjlCtRTzy3w"`

<br>

# Como configurar o Secret Manager

## 1º Passo 
Adicione a seguinte variavel ao seu ambiente (.env): 
    
    GOOGLE_APPLICATION_CREDENTIALS=/home/$USER/.service-accounts/secret-manager-account.json

onde secret-manager-account.json é uma conta de serviço na GCP que deve possuir ao menos as seguintes permissões:
    
`secretmanager.secrets.create`<br>
`secretmanager.secrets.get`<br>
`secretmanager.versions.access`<br>
`secretmanager.versions.add`<br>
`secretmanager.versions.list`<br>

sugerido utilizar o papel: 

`Administrador do Gerenciador de secrets`

<hr>

## 2º Passo

Onde precisar utilizá-la, importe a dependência com:

    <?php
    use Rubeus\SecretManagerGcp\SecretManager;


<br>