## Instalacja
Aby zainstalować Bettinger, należy posiadać: composer do obsługi paczek php oraz yarn do zarządzania paczkami JavaScript. Wymagane jest również PHP oraz baza danych MySQL.
### Instalacja Symfony
* W głównym katalogu wpisujemy w konsoli: `composer install`
* Duplikujemy plik `app/config/parameters.yml.dist`go i zmienamy nazwę na `parameter.yml` (należy dostosować parametry do własnego systemu)
* W konsoli wpisujemy: `php bin/console server:run` i serwer powinien wystartować pod adresem `localhost:8000`

### Instalacja Angulara
* Wchodzimy w konsoli do `web/` i wpisujemy `yarn install`
* Aby zbudować Front-End, wpisujemy w `web/`: `yarn run build`
* Aby włączyć automatyczne bodowanie aplikacji przy każdej zmianie plików, wpisujemy w `web/`: `yarn run build:watch`

### Baza danych
#### Tworzenie pustej bazy
W głównym katalogu projektu wpisujemy w konsoli: `php bin/console doctrine:database:create`
#### Aktualizacja schematu bazy
W głównym katalogu projketu wpisujemy w konsoli: `php bin/console doctrine:schema:update --force`

## Angular
###i18n, Internationalization
Aby przeszukać aplikację angulara w poszukiwaniu plików językowych, należy w /web wpisać:
`"./node_modules/.bin/ng-xi18n" --i18nFormat=xlf -p src/tsconfig.json`
Następnie przetłumaczyć `messages.xlf` na odpowiedni język i zapisać w formacie: `web/src/locale/messages.pl.xlf`

## Komendy
### Pobieranie meczów
Komenda: `php bin/console bettinger:download:matches`   
Działanie: póki co ładuje przykładowe mecze/drużyny/ligii, pózniej będzie pobierać dane o przyszłych meczach z innej strony

## API
### Rejestracja
Ścieżka: `api/register`  
Wymagane dane:  
*  email  
*  login 
*  password  
*  repassword

Zwracane dane:  
*    ok - określa, czy udało się zarejestrować 
*    error_code - kod błędu(  
        1 - login jest zajęty  
        2 - email jest zajety  
        3 - hasła się nie zgadzają  
        4 - hasło za krótkie  
)  
*    error_msg - opis błędu   
      
### Logowanie  
Ścieżka: `api/login`  
Wymagane dane:   
* username - login lub adres e-mail  
* password - hasło  
Zwracane dane:  
* ok - określa, czy udało się zalogować  
* error_code - kod błędu(  
            1 - użytkownik nie istnieje  
            2 - hasło się nie zgadza  
        )  
* error_msg - opis błędu  

### Wylogowywanie  
Ścieżka: `api/logout`  
Brak wymaganych danych  
Zwracane dane:  
*    1 - udało się wylogować  
*    0 - nie udało się wylogować  


## API - Ligi  
Ścieżka: `api/league`  
Brak wymaganych danych  
Zwracane dane:  
*    array - obiektów lig {id, name} 


## API - Drużyny
Ścieżka: `api/team`  
Brak wymaganych danych  
Zwracane dane:  
*    array - obiektów drużyn {id, name} 

## API - Mecze
### Mecze - tablica wszystkich meczy
Ścieżka: `api/game`  
Brak wymaganych danych  
Zwracane dane:  
*    array - obiektów meczy {id, date, teamOneScore, teamTwoScore, league, teamOne, teamTwo} 

### Mecze - tablica przefiltrowanych meczy
Ścieżka: `api/game/filter`  
Dane (wszystkie opcjonalnie):  
*  league - id ligi 
*  team - id drużyny
*  minDate - format (01-01-2000)
*  maxDate - format (01-01-2000)  

Zwracane dane:  
*    array - obiektów meczy {id, date, teamOneScore, teamTwoScore, league, teamOne, teamTwo} 

## API - Typy
### Typy - tablica wszystkich typów
Ścieżka: `api/bet`  
Brak wymaganych danych  

Zwracane dane:  
*    array - obiektów typów {id, cost, odds, stake, result, status, user, game} - gdy zalogowany użytkownik nie ma uprawnień do zobaczenia co obstwił typer, wtedy nie w tablicy nie ma zmiennej "result"

### Typy - tablica przefiltrowanych typów
Ścieżka: `api/bet/filter`  
Dane (wszystkie opcjonalnie):  
*  id - id typu 
*  game - id meczy
*  user - id typera
*  cost - maksymalny koszt  

Zwracane dane:  
*    array - obiektów typów {id, cost, odds, stake, result, status, user, game} - gdy zalogowany użytkownik nie ma uprawnień do zobaczenia co obstwił typer, wtedy w tablicy nie ma zmiennej "result"

### Typy - dodawanie typów
Ścieżka: `api/bet/add`  
Dane (wszystkie wymagane):  
*  cost - cena typu 
*  odds - kurs
*  stake - stawka
*  result - (0-remis, 1 - wygrywa gospodarz, 2-wygrywa gość)
*  game - id meczu  

Zwracane dane:  
*    ok - true/false - czy udało się dodać
  
## API - Typerzy
### Dane i statystyki na temat typera
Ścieżka: `api/tipster/show/{id}`  
Metoda: `GET`  
Dane: brak  
  
Zwracane dane:  
*    id  
*    login  
*    efficiency  
*    efficiency_last_3_month  
*    efficiency_last_month  
*    yield_value  
*    sold_single_bet  
*    sold_subscriptions  
*    count_of_currents_bets  
*    count_of_bets  
*    subscription_cost  
  
### Przefiltrowani i posortowani typerzy  
Ścieżka: `api/tipster/filter`    
Metoda: `POST`  
Dane (wszystkie opcjonalne):  
*  sortedBy : string {efficiency, efficiency_last_3_month, ..itd}  
*  orderBy : int {3, 4) - 3 (rosnąco)/ 4 (malejąco)  
*  filters : array { maxPrice : value , minPrice: value, login: string, league: int }  
  
Zwracane dane:  
*  tablica z obiektami typerów

## API - Ustawienia
### Dane na temat ustawień typera
Ścieżka: `api/settings`  
Metoda: `GET`  
Dane: brak  
  
Zwracane dane:  
*    id  
*    login  
*    subscription_cost  
*    image  
*    about  
  
### Edycja ustawień 
Ścieżka: `api/settings/edit`    
Metoda: `POST`  
Dane (wszystkie opcjonalne):  
*  subscription_cost : float  
*  password : string  
*  repassword : string  
*  about : string  
  
Zwracane dane:  
*  ok : int {0, 1}  
*  errorCode : int  {  
                     1 - nieprawidłowa wartość subscription_cost,  
                     2 - hasła do siebie nie pasują,  
                     3 - hasło za krótkie,  
                     4 - upload avatara się nie powiódł  
                     }  


