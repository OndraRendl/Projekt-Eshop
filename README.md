# E-shop Apple
[Návod na spuštění eshopu zde](navod.txt)


**1. Stručný popis programu, jeho funkce, ukázky použití**
>**Stručný popis programu**
- Tento e-shop zaměřený na produkty Apple umožňuje uživatelům prohlížet a vybírat produkty Apple, spravovat své objednávky a upravovat své uživatelské údaje. Uživatelé mohou zobrazit všechny své objednávky a upravovat například své jméno, příjmení či heslo. Administrátoři mají rozšířená práva, umožňující jim mazat produkty z webu a v aplikaci přidávat nové produkty, stejně jako upravovat skladové zásoby (přidávat nové kusy na sklad).

- Celý systém je propojen s databází MySQL, která obsahuje tabulky pro produkty, uživatele a objednávky. Aplikace komunikuje s touto databází, aby prováděla operace jako výběr produktů, správu uživatelských údajů a manipulaci se skladovými zásobami.
>**Funkce programu**
- Prohlížení produktů Apple:
  Uživatelé mohou procházet seznam produktů, který obsahuje názvy, popisy, ceny a obrázky.
- Výběr produktů a objednávky:
  Uživatelé si mohou vybrat produkty, přidat je do košíku a provést objednávku.
  Objednávky jsou uloženy v databázi a uživatelé mohou vidět historii svých objednávek.
- Správa uživatelských údajů:
  Uživatelé mohou upravit své osobní údaje (např. jméno, e-mail, heslo) a spravovat svůj účet.
- Správa produktů pro administrátory:
  Administrátoři mohou přidávat nové produkty do systému a upravovat jejich detaily (např. název, cena, popis, obrázek).
  Administrátoři mohou mazat produkty z nabídky e-shopu.
- Správa skladových zásob:
  Administrátoři mohou přidávat nové kusy produktů na sklad (např. přidání +10 kusů konkrétního produktu).
- Komunikace s databází:
  Všechny informace (produkty, objednávky, uživatelské údaje) jsou uloženy v databázi MySQL.
  Aplikace provádí CRUD operace (vytváření, čtení, aktualizace, mazání) na tabulkách produktů, uživatelů a objednávek.
- Autentifikace uživatelů:
  Uživatelé se přihlašují do systému pomocí e-mailu a hesla.
  Správa přístupu k různým funkcím na základě role (uživatel vs. administrátor).
>**Ukázky použití**
![obr1](ukazka/ukazka1.jpg)
**Obrázek č. 1 - Stránka eshopu s produkty (produkty se načítají z databáze)**
![obr2](ukazka/ukazka2.jpg)
**Obrázek č. 2 - Stránka s produktem**
![obr3](ukazka/ukazka3.jpg)
**Obrázek č. 3 - Košík s produktem**
![obr4](ukazka/ukazka4.jpg)
**Obrázek č. 4 - Stránka se souhrnem objednávky**
![obr5](ukazka/ukazka5.jpg)
**Obrázek č. 5 - Uživatelská část - objednávky uživatele**
![obr6](ukazka/ukazka6.jpg)
**Obrázek č. 6 - Uživatelská část - osobbní údaje uživatele + možnost změny některých údajů**
![obr7](ukazka/ukazka7.jpg)
**Obrázek č. 7 - Webová stránka pro admina, kde může mazat zboží (z databáze), které je na eshopu**
![obr8](ukazka/ukazka8.jpg)
**Obrázek č. 8 - Aplikace, možnost přidání nebo odebrání kusů na skladě (komunikace s databází)**




**2. Seznam použitých algoritmů a knihoven**

**3. Seznam autorů**
- Ondřej Rendl

**4. Dokumentace kódu Python (minimálně dokumentace funkcí a tříd)**
>**Popis třídy App**
-  Třída App je hlavní aplikací pro správu e-shopu, která poskytuje grafické rozhraní pro interakci s databází produktů, objednávek a uživatelů. Obsahuje funkce pro zobrazení produktů v tabulce, jejich přidávání a odebírání, stejně jako zobrazení objednávek a generování grafů.
-  App
  - Třída App spravuje hlavní uživatelské rozhraní e-shopu, které umožňuje správu produktů a objednávek a vykreslení grafů o skladových zásobách.
>**Popis funkce fetch_products()**
-  fetch_products()
-  Tato funkce načte všechny produkty z databáze a vrátí je ve formě seznamu, kde každý produkt je uložen jako tuple s informacemi o id, názvu, popisu, ceně, obrázku a skladovém množství produktu.

>**Popis funkce fetch_orders()**
-  fetch_orders()
-  Tato funkce načte všechny objednávky z databáze a vrátí je ve formě seznamu, kde každá objednávka je tuple s informacemi jako id, jméno zákazníka, adresa, telefonní číslo a detaily objednávky.

>**Popis funkce insert_product()**
-  insert_product(name, description, price, image, stock)
-  Tato funkce slouží k vložení nového produktu do databáze.
-  Parametry:

-  name (str): Název produktu, který bude zobrazen na webu.
-  description (str): Podrobný popis produktu.
-  price (float): Cena produktu.
-  image (str): Cesta k obrázku produktu, který bude zobrazen na stránce.
-  stock (int): Počet kusů produktu, které jsou aktuálně na skladě.


>**Popis funkce update_table()**
-  update_table()
-  Tato metoda načte produkty z databáze a aktualizuje tabulku v grafickém rozhraní. Nastaví počet řádků a sloupců v tabulce podle počtu produktů a naplní jednotlivé buňky daty o produktu.

>**Popis funkce download_orders()**
-  download_orders()
-  Tato funkce umožňuje uživateli stáhnout všechny objednávky z databáze do textového souboru. Používá grafické okno pro výběr cílové složky a souboru.

>**Popis funkce add_product_to_stock()**
-  add_product_to_stock(product_id, quantity)
-  Tato funkce přidá zadané množství kusů produktu na sklad. Pomocí SQL příkazu se aktualizuje hodnoty pro daný produkt v databázi.

>**Popis funkce remove_product_from_stock()**
-  remove_product_from_stock(product_id, quantity)
-  Tato funkce odebere zadané množství kusů produktu ze skladu. Pokud je na skladě dostatek kusů, hodnoty se v databázi aktualizují.

>**Popis funkce add_order()**
-  add_order(customer_name, customer_email, order_details)
-  Tato funkce slouží k přidání nové objednávky do databáze. Objednávka obsahuje informace o zákazníkovi a produktech, které objednal.

>**Popis funkce generate_report()**
-  generate_report()
-  Tato funkce generuje report o skladových zásobách a objednávkách, který lze exportovat do souboru.

**ER-diagram databáze**

**5. Skripty pro tvorbu tabulek, vložení několika vzorových dat a získání vzorových dat z tabulek**
>**Skripty pro vytvoření hlavních databází**
-  Vytvoření databáze **users**
```sql
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    username VARCHAR(255) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```
-  Vytvoření databáze **produkty**
```sql
CREATE TABLE IF NOT EXISTS produkty (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nazev VARCHAR(255) NOT NULL,
    popis TEXT,
    cena DECIMAL(10,2) NOT NULL,
    obrazek VARCHAR(255),
    skladem INT NOT NULL DEFAULT 0
);
```
-  Vytvoření databáze **orders** (přidávat skripty na vytvoření databáze postupně)
```sql
CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `zip` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `shipping_method` varchar(100) NOT NULL,
  `products` text NOT NULL
)
```
```sql
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`);
```
```sql
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;
```
```sql
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON DELETE CASCADE;
COMMIT;
```



**6. Úprava data v databázi (UPDATE, DELETE)**

  
