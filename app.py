from PyQt5.QtCore import QTimer
import pymysql
from PyQt5.QtWidgets import QApplication, QWidget, QVBoxLayout, QTableWidget, QTableWidgetItem, QPushButton, QLineEdit, QTabWidget, QFormLayout

# Připojení k databázi pro produkty
connection = pymysql.connect(
    host='localhost',
    user='root',  # Zadej své uživatelské jméno
    password='',  # Zadej své heslo
    database='e-shopapple'
)

# Připojení k databázi pro historii objednávek
order_connection = pymysql.connect(
    host='localhost',
    user='root',  # Zadej své uživatelské jméno
    password='',  # Zadej své heslo
    database='objednavky'  # Databáze pro historii objednávek
)

def fetch_products():
    """
    Načte všechny produkty z databáze.
    
    Returns:
        list: Seznam produktů, každý produkt je uložen jako tuple (id, název, popis, cena, obrázek, skladem).
    """
    with connection.cursor() as cursor:
        cursor.execute("SELECT * FROM produkty")
        products = cursor.fetchall()
    
    return products

def insert_product(name, description, price, image, stock):
    """
    Vloží nový produkt do databáze.
    """
    try:
        with connection.cursor() as cursor:
            query = "INSERT INTO produkty (nazev, popis, cena, obrazek, skladem) VALUES (%s, %s, %s, %s, %s)"
            cursor.execute(query, (name, description, price, image, stock))
            connection.commit()
    except Exception as e:
        print(f"Chyba při vkládání produktu: {e}")

def fetch_orders():
    """
    Načte všechny objednávky z databáze.
    
    Returns:
        list: Seznam objednávek, každá objednávka je uložená jako tuple (id, jméno, adresa, město, PSČ, email, telefon, metoda platby, celková cena, datum objednávky, způsob dopravy, produkty).
    """
    with order_connection.cursor() as cursor:
        cursor.execute("SELECT * FROM orders")
        orders = cursor.fetchall()
    
    return orders

class App(QWidget):
    def __init__(self):
        super().__init__()

        self.setWindowTitle("E-shop App")
        self.setGeometry(100, 100, 900, 500)  # Zvýšená velikost okna pro větší rozložení

        # Hlavní layout
        self.layout = QVBoxLayout()

        # Nastavení tabulky pro produkty
        self.tabs = QTabWidget(self)  # Vytvoření widgetu pro záložky
        self.layout.addWidget(self.tabs)

        # Vytvoření záložky pro Stav skladu
        self.stock_tab = QWidget()
        self.tabs.addTab(self.stock_tab, "Stav skladu")
        self.stock_layout = QVBoxLayout(self.stock_tab)

        self.table = QTableWidget(self)
        self.stock_layout.addWidget(self.table)

        # Vytvoření záložky pro Příjem produktů
        self.receiving_tab = QWidget()
        self.tabs.addTab(self.receiving_tab, "Příjem produktů")
        self.receiving_layout = QVBoxLayout(self.receiving_tab)

        # Formulář pro přidání nového produktu
        self.form_layout = QFormLayout()

        self.name_input = QLineEdit(self)
        self.description_input = QLineEdit(self)
        self.price_input = QLineEdit(self)
        self.image_input = QLineEdit(self)
        self.stock_input = QLineEdit(self)

        self.form_layout.addRow("Název:", self.name_input)
        self.form_layout.addRow("Popis:", self.description_input)
        self.form_layout.addRow("Cena:", self.price_input)
        self.form_layout.addRow("Obrázek:", self.image_input)
        self.form_layout.addRow("Skladem:", self.stock_input)

        self.add_product_button = QPushButton("Přidat produkt", self)
        self.add_product_button.clicked.connect(self.add_product)

        self.receiving_layout.addLayout(self.form_layout)
        self.receiving_layout.addWidget(self.add_product_button)

        # Vytvoření záložky pro Historii objednávek
        self.orders_tab = QWidget()
        self.tabs.addTab(self.orders_tab, "Historie objednávek")
        self.orders_layout = QVBoxLayout(self.orders_tab)

        self.orders_table = QTableWidget(self)
        self.orders_layout.addWidget(self.orders_table)

        self.update_table()  # Inicializace tabulky pro produkty
        self.update_orders_table()  # Inicializace tabulky pro objednávky

        # Nastavení timeru pro pravidelnou aktualizaci tabulky
        self.timer = QTimer(self)
        self.timer.timeout.connect(self.update_table)  # Funkce pro aktualizaci tabulky produktů
        self.timer.start(5000)  # Aktualizace každých 5 sekund (5000 ms)

        self.setLayout(self.layout)

        # Vylepšení vzhledu
        self.setStyleSheet("""
            QWidget {
                font-family: 'Arial', sans-serif;
                background-color: #f4f4f9;
            }

            QTableWidget {
                background-color: #ffffff;
                border-radius: 10px;
                border: 1px solid #ddd;
                padding: 5px;
                min-height: 400px;  # Nastavení minimální výšky tabulky
            }

            QTableWidget::item {
                padding: 10px;
            }

            QTableWidget::horizontalHeader {
                background-color: #4CAF50;
                color: white;
                font-size: 14px;
                padding: 5px;
            }

            QTableWidget::horizontalHeader::section {
                padding-left: 10px;
                padding-right: 10px;
            }

            QPushButton {
                background-color: #4CAF50;
                color: white;
                font-size: 14px;
                border-radius: 5px;
                padding: 5px 10px;
                margin: 5px;
            }

            QPushButton:hover {
                background-color: #45a049;
            }

            QLineEdit {
                padding: 5px;
                font-size: 14px;
                border-radius: 5px;
                border: 1px solid #ddd;
            }

            QTableWidget QTableWidget::item:selected {
                background-color: #FFFF99;  # Žlutá barva pro výběr v tabulce
            }
        """)

    def update_table(self):
        """
        Načte produkty z databáze a aktualizuje tabulku.
        """
        print("Aktualizace tabulky...")  # Přidáme logování pro ověření, zda se funkce skutečně spustí
        products = fetch_products()  # Načtení produktů z databáze
        
        if not products:
            print("Nebyla nalezena žádná data.")  # Kontrola, zda jsou nějaká data
            return

        # Nastavení počtu řádků a sloupců v tabulce pro produkty
        self.table.setRowCount(len(products))
        self.table.setColumnCount(9)  # Sloupce pro produkty
        self.table.setHorizontalHeaderLabels(
            ["ID", "Název", "Popis", "Cena", "Obrázek", "Skladem", "Změna", "Přidat", "Odebrat"]
        )

        # Nastavení šířky sloupců
        self.table.setColumnWidth(0, 50)  # ID
        self.table.setColumnWidth(1, 150)  # Název
        self.table.setColumnWidth(2, 200)  # Popis
        self.table.setColumnWidth(3, 100)  # Cena
        self.table.setColumnWidth(4, 150)  # Obrázek
        self.table.setColumnWidth(5, 100)  # Skladem
        self.table.setColumnWidth(6, 100)  # Změna
        self.table.setColumnWidth(7, 100)  # Přidat
        self.table.setColumnWidth(8, 100)  # Odebrat

        # Naplnění tabulky daty z databáze produktů
        for row, product in enumerate(products):
            for col, value in enumerate(product):
                self.table.setItem(row, col, QTableWidgetItem(str(value)))

            # Vytvoření pole pro zadání množství pro přidání/odebrání
            quantity_input = QLineEdit(self)
            self.table.setCellWidget(row, 6, quantity_input)

            # Vytvoření tlačítka pro přidání kusů
            add_button = QPushButton("Přidat", self)
            add_button.clicked.connect(self.create_add_button_function(row, quantity_input))
            self.table.setCellWidget(row, 7, add_button)

            # Vytvoření tlačítka pro odebrání kusů
            subtract_button = QPushButton("Odebrat", self)
            subtract_button.clicked.connect(self.create_subtract_button_function(row, quantity_input))
            self.table.setCellWidget(row, 8, subtract_button)

    def update_orders_table(self):
        """
        Načte objednávky z databáze a aktualizuje tabulku.
        """
        print("Aktualizace tabulky objednávek...")  # Přidáme logování pro ověření, zda se funkce skutečně spustí
        orders = fetch_orders()  # Načtení objednávek z databáze
        
        if not orders:
            print("Nebyla nalezena žádná data.")  # Kontrola, zda jsou nějaká data
            return

        # Nastavení počtu řádků a sloupců v tabulce pro objednávky
        self.orders_table.setRowCount(len(orders))
        self.orders_table.setColumnCount(12)  # Sloupce pro objednávky
        self.orders_table.setHorizontalHeaderLabels(
            ["ID", "Jméno", "Adresa", "Město", "PSČ", "Email", "Telefon", "Platba", "Celková cena", "Datum", "Doprava", "Produkty"]
        )

        # Naplnění tabulky daty z databáze objednávek
        for row, order in enumerate(orders):
            for col, value in enumerate(order):
                self.orders_table.setItem(row, col, QTableWidgetItem(str(value)))

        # Nastavení vzhledu tabulky
        for row in range(len(orders)):
            self.orders_table.setRowHeight(row, 25)  # Snížení výšky každého řádku na 25px (můžeš upravit dle potřeby)

        for col in range(12):
            self.orders_table.setColumnWidth(col, 150)  # Zvýšení šířky sloupců pro pohodlnější zobrazení

    def create_add_button_function(self, row, input_field):
        """
        Funkce pro přidání kusů na sklad pro daný řádek.
        """
        def add_to_stock():
            try:
                quantity = int(input_field.text())  # Získání hodnoty z pole
            except ValueError:
                print("Zadaná hodnota není číslo.")  # Pokud není číslo
                return
            
            product_id = self.table.item(row, 0).text()  # Získání ID produktu
            query = f"UPDATE produkty SET skladem = skladem + {quantity} WHERE id = {product_id}"

            # Provedení SQL dotazu
            with connection.cursor() as cursor:
                cursor.execute(query)
                connection.commit()

            # Aktualizace tabulky po změně
            self.update_table()

        return add_to_stock

    def create_subtract_button_function(self, row, input_field):
        """
        Funkce pro odebrání kusů ze skladu pro daný řádek.
        """
        def subtract_from_stock():
            try:
                quantity = int(input_field.text())  # Získání hodnoty z pole
            except ValueError:
                print("Zadaná hodnota není číslo.")  # Pokud není číslo
                return
            
            product_id = self.table.item(row, 0).text()  # Získání ID produktu
            query = f"UPDATE produkty SET skladem = skladem - {quantity} WHERE id = {product_id}"

            # Provedení SQL dotazu
            with connection.cursor() as cursor:
                cursor.execute(query)
                connection.commit()

            # Aktualizace tabulky po změně
            self.update_table()

        return subtract_from_stock

    def add_product(self):
        """
        Funkce pro přidání nového produktu.
        """
        name = self.name_input.text()
        description = self.description_input.text()
        try:
            price = float(self.price_input.text())
        except ValueError:
            print("Cena musí být číslo.")
            return
        image = self.image_input.text()
        try:
            stock = int(self.stock_input.text())
        except ValueError:
            print("Počet na skladě musí být celé číslo.")
            return

        insert_product(name, description, price, image, stock)

        # Vyčištění formuláře po úspěšném přidání produktu
        self.name_input.clear()
        self.description_input.clear()
        self.price_input.clear()
        self.image_input.clear()
        self.stock_input.clear()

        # Aktualizace tabulky produktů
        self.update_table()

# Spuštění aplikace
if __name__ == "__main__":
    app = QApplication([])
    window = App()
    window.show()
    app.exec_()
