from PyQt5.QtCore import QTimer
import pymysql
from PyQt5.QtWidgets import QApplication, QWidget, QVBoxLayout, QTableWidget, QTableWidgetItem, QPushButton, QLineEdit, QTabWidget, QFormLayout, QFileDialog, QDialog, QLabel, QVBoxLayout, QMessageBox, QHBoxLayout, QComboBox
import matplotlib.pyplot as plt
from matplotlib.backends.backend_qt5agg import FigureCanvasQTAgg as FigureCanvas

# Připojení k databázi pro produkty
connection = pymysql.connect(
    host='localhost',
    user='root',  # Zadej své uživatelské jméno
    password='',  # Zadej své heslo
    database='eshop'
)

# Připojení k databázi pro historii objednávek
order_connection = pymysql.connect(
    host='localhost',
    user='root',  # Zadej své uživatelské jméno
    password='',  # Zadej své heslo
    database='eshop'  # Databáze pro historii objednávek
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

def fetch_last_10_orders():
    """
    Načte posledních 10 objednávek z databáze.
    
    Returns:
        list: Seznam posledních 10 objednávek, každá objednávka je uložená jako tuple.
    """
    with order_connection.cursor() as cursor:
        cursor.execute("SELECT * FROM orders ORDER BY order_date DESC LIMIT 10")
        orders = cursor.fetchall()
    
    return orders

class App(QWidget):
    def __init__(self):
        super().__init__()

        self.setWindowTitle("E-shop App")
        self.setGeometry(100, 100, 900, 700)  # Zvýšená velikost okna pro větší rozložení

        # Hlavní layout
        self.layout = QVBoxLayout()

        # Nastavení tabulky pro produkty
        self.tabs = QTabWidget(self)  
        self.layout.addWidget(self.tabs)

        # Vytvoření záložky pro Stav skladu
        self.stock_tab = QWidget()
        self.tabs.addTab(self.stock_tab, "Stav skladu")
        self.stock_layout = QVBoxLayout(self.stock_tab)

        self.table = QTableWidget(self)
        self.stock_layout.addWidget(self.table)

        # Tlačítko pro ruční aktualizaci dat
        self.refresh_button = QPushButton("Obnovit data", self)
        self.refresh_button.clicked.connect(self.update_table)
        self.stock_layout.addWidget(self.refresh_button)

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

        # Tlačítko pro stažení objednávek
        self.download_button = QPushButton("Stáhnout objednávky", self)
        self.download_button.clicked.connect(self.download_orders)
        self.orders_layout.addWidget(self.download_button)

        # Vytvoření záložky pro grafy
        self.graphs_tab = QWidget()
        self.tabs.addTab(self.graphs_tab, "Grafy")
        self.graphs_layout = QVBoxLayout(self.graphs_tab)

        # Vytvoření grafu pro vizualizaci
        self.figure = plt.Figure(figsize=(5, 3), dpi=100)
        self.canvas = FigureCanvas(self.figure)
        self.graphs_layout.addWidget(self.canvas)

        # Zisk objednávek
        self.profit_label = QLabel("Zisk objednávek: 0", self)
        self.graphs_layout.addWidget(self.profit_label)

        self.update_table()  # Inicializace tabulky pro produkty
        self.update_orders_table()  # Inicializace tabulky pro objednávky
        self.update_graph()  # Vykreslí graf při spuštění aplikace

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
                min-height: 400px;
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

            QPushButton {
                background-color: #4CAF50;
                color: white;
                font-size: 14px;
                border-radius: 5px;
                padding: 5px 10px;
                margin: 5px;
            }

            QLineEdit {
                padding: 5px;
                font-size: 14px;
                border-radius: 5px;
                border: 1px solid #ddd;
            }
        """)

    def update_table(self):
        """
        Načte produkty z databáze a aktualizuje tabulku.
        """
        print("Aktualizace tabulky...")
        products = fetch_products()
        
        if not products:
            print("Nebyla nalezena žádná data.")
            return

        # Nastavení počtu řádků a sloupců v tabulce pro produkty
        self.table.setRowCount(len(products))
        self.table.setColumnCount(9)
        self.table.setHorizontalHeaderLabels(
            ["ID", "Název", "Popis", "Cena", "Obrázek", "Skladem", "Změna", "Přidat", "Odebrat"]
        )

        # Naplnění tabulky daty z databáze produktů
        for row, product in enumerate(products):
            for col, value in enumerate(product):
                self.table.setItem(row, col, QTableWidgetItem(str(value)))
                
            self.table.setRowHeight(row, 55)

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
        print("Aktualizace tabulky objednávek...")
        orders = fetch_orders()
        
        if not orders:
            print("Nebyla nalezena žádná data.")
            return

        self.orders_table.setRowCount(len(orders))
        self.orders_table.setColumnCount(12)
        self.orders_table.setHorizontalHeaderLabels(
            ["ID", "Jméno", "Adresa", "Město", "PSČ", "Email", "Telefon", "Platba", "Celková cena", "Datum", "Doprava", "Produkty"]
        )

        for row, order in enumerate(orders):
            for col, value in enumerate(order):
                self.orders_table.setItem(row, col, QTableWidgetItem(str(value)))

    def update_graph(self):
        """
        Vykreslí graf cen posledních 10 objednávek.
        """
        print("Vykreslení grafu...")
        orders = fetch_last_10_orders()
        
        if not orders:
            print("Nebyla nalezena žádná data pro graf.")
            return

        # Získání cen posledních 10 objednávek
        prices = [float(order[9]) for order in orders]  # Používáme index 9 pro total_price
        order_indices = list(range(1, len(orders) + 1))  # Použijeme indexy 1 až 10

        ax = self.figure.add_subplot(111)
        ax.clear()
        ax.bar(order_indices, prices)
        ax.set_title("Ceny posledních 10 objednávek")
        ax.set_xlabel("Pořadí objednávky")
        ax.set_ylabel("Cena")

        # Zobrazení zisku
        total_price = sum(prices)
        self.profit_label.setText(f"Zisk posledních 10 objednávek: {total_price}")

        self.canvas.draw()

    def download_orders(self):
        """
        Stáhne objednávky do textového souboru.
        """
        options = QFileDialog.Options()
        file_path, _ = QFileDialog.getSaveFileName(self, "Uložit objednávky", "", "Text Files (*.txt);;All Files (*)", options=options)

        if file_path:
            orders = fetch_orders()

            with open(file_path, 'w', encoding='utf-8') as file:
                # Zápis hlavičky
                file.write("\t".join(["ID", "Jméno", "Adresa", "Město", "PSČ", "Email", "Telefon", "Platba", "Celková cena", "Datum", "Doprava", "Produkty"]) + "\n")

                # Zápis každé objednávky
                for order in orders:
                    file.write("\t".join(map(str, order)) + "\n")

            print(f"Objednávky byly úspěšně staženy do souboru: {file_path}")

    def create_add_button_function(self, row, quantity_input):
        """
        Vytvoří funkci pro tlačítko 'Přidat' pro specifikovaný produkt a množství.
        """
        def add_product():
            try:
                quantity = int(quantity_input.text())
                product_id = self.table.item(row, 0).text()

                if quantity > 0:
                    self.add_quantity_to_product(product_id, quantity)
                    print(f"Přidáno {quantity} ks produktu ID {product_id}.")
                else:
                    QMessageBox.warning(self, "Chyba", "Zadejte platné množství.")
            except ValueError:
                QMessageBox.warning(self, "Chyba", "Zadejte platné číslo.")

        return add_product

    def create_subtract_button_function(self, row, quantity_input):
        """
        Vytvoří funkci pro tlačítko 'Odebrat' pro specifikovaný produkt a množství.
        """
        def subtract_product():
            try:
                quantity = int(quantity_input.text())
                product_id = self.table.item(row, 0).text()
                current_stock = int(self.table.item(row, 5).text())

                if quantity > 0 and current_stock - quantity >= 0:
                    self.subtract_quantity_from_product(product_id, quantity)
                    print(f"Odebráno {quantity} ks produktu ID {product_id}.")
                else:
                    QMessageBox.warning(self, "Chyba", "Nedostatečné množství na skladě nebo neplatné množství.")
            except ValueError:
                QMessageBox.warning(self, "Chyba", "Zadejte platné číslo.")

        return subtract_product

    def add_quantity_to_product(self, product_id, quantity):
        """
        Přidá množství pro daný produkt.
        """
        with connection.cursor() as cursor:
            cursor.execute(f"UPDATE produkty SET skladem = skladem + {quantity} WHERE id = {product_id}")
            connection.commit()

        self.update_table()

    def subtract_quantity_from_product(self, product_id, quantity):
        """
        Odebere množství pro daný produkt.
        """
        with connection.cursor() as cursor:
            cursor.execute(f"UPDATE produkty SET skladem = skladem - {quantity} WHERE id = {product_id}")
            connection.commit()

        self.update_table()

    def add_product(self):
        """
        Přidá nový produkt do databáze.
        """
        name = self.name_input.text()
        description = self.description_input.text()
        price = self.price_input.text()
        image = self.image_input.text()
        stock = self.stock_input.text()

        if name and description and price and stock:
            try:
                price = float(price)  # Ujistěte se, že cena je číslo
                stock = int(stock)    # Ujistěte se, že skladem je celé číslo
                insert_product(name, description, price, image, stock)
                print("Produkt byl úspěšně přidán.")
                self.name_input.clear()
                self.description_input.clear()
                self.price_input.clear()
                self.image_input.clear()
                self.stock_input.clear()
                self.update_table()  # Obnoví tabulku po přidání produktu
                self.tabs.setCurrentWidget(self.stock_tab)  # Přepne na záložku "Stav skladu"
            except ValueError:
                QMessageBox.warning(self, "Chyba", "Cena musí být číslo a skladem musí být celé číslo.")
        else:
            QMessageBox.warning(self, "Chyba", "Vyplňte všechna pole.")

class LoginDialog(QDialog):
    def __init__(self):
        super().__init__()
        self.setWindowTitle("Přihlášení")
        self.setGeometry(100, 100, 300, 150)

        self.layout = QVBoxLayout()

        self.username_label = QLabel("Uživatelské jméno:")
        self.username_input = QLineEdit(self)
        self.password_label = QLabel("Heslo:")
        self.password_input = QLineEdit(self)
        self.password_input.setEchoMode(QLineEdit.Password)

        self.layout.addWidget(self.username_label)
        self.layout.addWidget(self.username_input)
        self.layout.addWidget(self.password_label)
        self.layout.addWidget(self.password_input)

        self.login_button = QPushButton("Přihlásit se", self)
        self.login_button.clicked.connect(self.check_credentials)
        self.layout.addWidget(self.login_button)

        self.setLayout(self.layout)

    def check_credentials(self):
        username = self.username_input.text()
        password = self.password_input.text()

        try:
            with open('appreg.txt', 'r') as file:
                lines = file.readlines()
                stored_username = lines[0].strip()
                stored_password = lines[1].strip()

                if username == stored_username and password == stored_password:
                    self.accept()
                else:
                    QMessageBox.warning(self, "Chyba", "Nesprávné uživatelské jméno nebo heslo.")
        except FileNotFoundError:
            QMessageBox.critical(self, "Chyba", "Soubor appreg.txt nebyl nalezen.")
        except IndexError:
            QMessageBox.critical(self, "Chyba", "Soubor appreg.txt je poškozený.")

if __name__ == '__main__':
    app = QApplication([])
    login_dialog = LoginDialog()

    if login_dialog.exec_() == QDialog.Accepted:
        window = App()
        window.show()
        app.exec_()