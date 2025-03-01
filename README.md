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
>**1. Importy**
-  PyQt5.QtCore.QTimer: Slouží k časování.
-  pymysql: Používá se pro připojení a práci s MySQL databází.
-  PyQt5.QtWidgets: Obsahuje widgety jako QVBoxLayout, QPushButton, QTableWidget, atd.
-  matplotlib.pyplot: Slouží pro vytváření grafů.
-  matplotlib.backends.backend_qt5agg.FigureCanvasQTAgg: Používá se pro vykreslování grafů do Qt aplikace.

>**2. Připojení k databázi**
-  connection: Připojení k databázi pro produkty (eshop).
-  order_connection: Připojení k databázi pro objednávky (eshop).

>**3. Funkce pro práci s databází**
```python
fetch_products()
```
-  Popis: Načte všechny produkty z databáze.
-  Výstup: Seznam produktů jako tuple (id, název, popis, cena, obrázek, skladem).
```python
insert_product(name, description, price, image, stock)
```
-  Popis: Vloží nový produkt do databáze.
-  Parametry: name, description, price, image, stock – vlastnosti nového produktu.
```python
fetch_orders()
```
-  Popis: Načte všechny objednávky z databáze.
-  Výstup: Seznam objednávek jako tuple (id, jméno, adresa, město, PSČ, email, telefon, metoda platby, celková cena, datum objednávky, způsob dopravy, produkty).
```python
fetch_last_10_orders()
```
-  Popis: Načte posledních 10 objednávek.
-  Výstup: Seznam posledních 10 objednávek.

>**4. Hlavní třída aplikace App(QWidget)**
```python
__init__(self)
```
-  Popis: Inicializuje aplikaci, nastavuje vzhled a připojuje všechny widgety a layouty.
-  Tlačítka a layouty:
-  Tabulky: Tři tabulky pro zobrazení produktů, objednávek a grafů.
-  Záložky: Záložky pro "Stav skladu", "Příjem produktů", "Historii objednávek", a "Grafy".
```python
update_table(self)
```
-  Popis: Načte produkty z databáze a aktualizuje tabulku.
-  Popis práce: Pro každý produkt je vytvořen řádek v tabulce s hodnotami produktů a tlačítky pro přidání nebo odebrání kusů z inventáře.
```python
update_orders_table(self)
```
-  Popis: Načte objednávky z databáze a aktualizuje tabulku.
```python
update_graph(self)
```
-  Popis: Vykreslí graf s cenami posledních 10 objednávek.
-  Detail: Vykresluje sloupcový graf a zobrazuje zisk a průměrnou cenu objednávek.
```python
download_orders(self)
```
-  Popis: Umožňuje stáhnout objednávky do textového souboru.
```python
create_add_button_function(self, row, quantity_input)
```
-  Popis: Vytvoří funkci pro tlačítko "Přidat" pro daný produkt.
-  Funkčnost: Přidá zadané množství kusů na sklad.
```python
create_subtract_button_function(self, row, quantity_input)
```
-  Popis: Vytvoří funkci pro tlačítko "Odebrat" pro daný produkt.
-  Funkčnost: Odebere zadané množství kusů ze skladu.
```python
add_quantity_to_product(self, product_id, quantity)
```
-  Popis: Přidá specifikované množství k produktu.
```python
subtract_quantity_from_product(self, product_id, quantity)
```
-  Popis: Odebere specifikované množství od produktu.
```python
add_product(self)
```
-  Popis: Přidá nový produkt do databáze na základě údajů z formuláře.
-  Validace: Kontroluje, zda je cena číslo a skladem celé číslo.

>**5. Třída pro přihlášení LoginDialog(QDialog)**
```python
__init__(self)
```
-  Popis: Vytvoří okno pro přihlášení uživatele.
-  Komponenty: Uživatelské jméno, heslo, tlačítko pro přihlášení.
```python
check_credentials(self)
```
-  Popis: Ověří uživatelské jméno a heslo podle hodnot v souboru appreg.txt.
>**6. Spuštění aplikace**
-  Popis: Pokud uživatel úspěšně zadá přihlašovací údaje, otevře se hlavní okno aplikace, jinak se aplikace uzavře.







**5. ER-diagram databáze**

**6. Skripty pro tvorbu tabulek, vložení několika vzorových dat a získání vzorových dat z tabulek**
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
-  Vytvoření databáze **orders** (přidávat skripty na vytvoření databáze postupně, jinak nebude fungovat DELETE ON CASCADE)
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
>**Skripty pro vložení několik vzorových dat**
-  Databáze **users**
```sql
INSERT INTO users (first_name, last_name, username, email, password) VALUES
('admin', 'admin', 'admin', 'admin@admin.com', 'admin'),
('Petr', 'Novák', 'pnovak', 'pnovak@gmail.com', '111'),
('Jana', 'Svobodová', 'jsvobodova', 'jsvobodova@gmail.com', '222');
```
-  Databáze **produkty**
```sql
INSERT INTO produkty (nazev, popis, cena, obrazek, skladem) VALUES
('iPhone 16 Pro Max 128GB', 'Nejnovější vlajková loď od Apple s 48 MP fotoaparátem, ultra výkonným A17 Bionic čipem, až 2TB úložištěm a 6,7" Super Retina XDR ProMotion displejem s maximálním jasem 2000 nitů. Ideální pro fotografie, hry i multitasking.', 34990.00, 'iphone-16-pro-max.jpg', 100),
('MacBook Pro 16" (2023)', 'Výkonný notebook s M2 Pro čipem, 16 GB RAM a 1 TB SSD úložištěm. Nabízí 16" Liquid Retina XDR displej, až 22hodinovou výdrž baterie a Thunderbolt 4 porty. Skvělý nástroj pro profesionály a kreativce.', 59990.00, 'macbook-pro-16.jpg', 100),
('iPad Air 5. generace', '10,9" Liquid Retina displej, chip M1, ideální pro práci i zábavu. Podporuje Apple Pencil 2. generace a Magic Keyboard. Perfektní kombinace výkonu a přenosnosti pro každodenní použití.', 15990.00, 'ipad-air-5.jpg', 100),
('Apple Watch Ultra', 'Chytré hodinky pro sportovce a outdoorové nadšence. Nabízí robustní design, 49mm titanové tělo, GPS, měření tepu, kyslíku v krvi a EKG. Podporují extrémní sportovní aktivity a vodotěsnost do 100m.', 18990.00, 'apple-watch-ultra.jpg', 100),
('AirPods Pro 2. generace', 'Bezdrátová sluchátka s aktivním potlačením hluku, až 6 hodin přehrávání na jedno nabití, vylepšený čip H2, adaptivní ekvalizér pro optimalizaci zvuku. Skvělé pro práci i zábavu.', 6990.00, 'airpods-pro-2.jpg', 100),
('Apple TV 4K (2022)', 'Streamovací zařízení pro 4K HDR obsah, podpora Dolby Vision a Dolby Atmos. Včetně Siri Remote s vyhledáváním hlasem a podpora široké škály aplikací pro streamování a zábavu.', 5990.00, 'apple-tv-4k.jpg', 100),
('iPhone 14 Plus 128GB', '6,7" Super Retina XDR displej, A15 Bionic chip, vylepšený fotoaparát a skvělá výdrž baterie. Ideální pro milovníky velkých displejů a výkonu.', 24990.00, 'iphone-14-plus.jpg', 100),
('iMac 24" (2021)', 'All-in-one počítač s Retina displejem, chipem M1 a až 16GB RAM. Skvélé pro domácí i profesionální použití, s moderním designem a rychlým výkonem.', 28990.00, 'imac-24.jpg', 100);
```
>**Skripty pro získaní vzorových dat z tabulek**
>**1. Výpis všech uživatelů seřazených podle data vytvoření (od nejnovějších)**
```sql
SELECT * FROM users ORDER BY created_at DESC;
```

**7. Úprava data v databázi (UPDATE, DELETE)**

  
