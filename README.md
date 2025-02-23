# E-shop Apple
**Stručný popis programu, jeho funkce, ukázky použití**
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

**Seznam použitých algoritmů a knihoven**

**Seznam autorů**
- Ondřej Rendl

**Dokumentace kódu Python (minimálně dokumentace funkcí a tříd)**
>**Popis třídy App**
-  Třída App je hlavní aplikací pro správu e-shopu, která poskytuje grafické rozhraní pro interakci s databází produktů, objednávek a uživatelů. Obsahuje funkce pro zobrazení produktů v tabulce, jejich přidávání a odebírání, stejně jako zobrazení objednávek a generování grafů.
>**App**
- Třída App spravuje hlavní uživatelské rozhraní e-shopu, které umožňuje správu produktů a objednávek a vykreslení grafů o skladových zásobách.









**ER-diagram databáze**

**Skripty pro tvorbu tabulek, vložení několika vzorových dat a získání vzorových dat z tabulek**

**Úprava data v databázi (UPDATE, DELETE)**

  
