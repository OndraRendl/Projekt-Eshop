from PyQt5.QtCore import QTimer
import pymysql
from PyQt5.QtWidgets import QApplication, QWidget, QVBoxLayout, QTableWidget, QTableWidgetItem, QPushButton, QLineEdit, QHBoxLayout, QTabWidget

# Připojení k databázi
connection = pymysql.connect(
    host='localhost',
    user='root',  # Zadej své uživatelské jméno
    password='',  # Zadej své heslo
    database='e-shopapple'
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

        # Tlačítko pro aktualizaci tabulky
        self.refresh_button = QPushButton("Aktualizovat tabulku", self)
        self.refresh_button.clicked.connect(self.update_table)
        self.stock_layout.addWidget(self.refresh_button)

        self.update_table()  # Inicializace tabulky

        # Nastavení timeru pro pravidelnou aktualizaci tabulky
        self.timer = QTimer(self)
        self.timer.timeout.connect(self.update_table)  # Funkce pro aktualizaci tabulky
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

        # Nastavení počtu řádků a sloupců v tabulce
        self.table.setRowCount(len(products))
        self.table.setColumnCount(9)  # Změna počtu sloupců (přidáme sloupce pro pole a tlačítka)
        self.table.setHorizontalHeaderLabels(
            ["ID", "Název", "Popis", "Cena", "Obrázek", "Skladem", "Změna", "Přidat", "Odebrat"]
        )

        # Nastavení výšky řádků a šířky sloupců pro větší velikost
        for row in range(len(products)):
            self.table.setRowHeight(row, 40)  # Zvýšení výšky každého řádku
        
        for col in range(9):
            self.table.setColumnWidth(col, 150)  # Zvýšení šířky sloupců pro pohodlnější zobrazení

        # Naplnění tabulky daty z databáze
        for row, product in enumerate(products):
            for col, value in enumerate(product):
                self.table.setItem(row, col, QTableWidgetItem(str(value)))

            # Vytvoření pole pro zadání množství pro přidání/odebrání
            quantity_input = QLineEdit(self)
            self.table.setCellWidget(row, 6, quantity_input)  # Nastavíme pole pro množství
            quantity_input.setStyleSheet("background-color: #FFFF99;")  # Nastavení žluté barvy pozadí

            # Vytvoření tlačítka pro přidání kusů
            add_button = QPushButton("Přidat", self)
            add_button.clicked.connect(self.create_add_button_function(row, quantity_input))
            self.table.setCellWidget(row, 7, add_button)  # Nastavíme tlačítko pro přidání

            # Vytvoření tlačítka pro odebrání kusů
            subtract_button = QPushButton("Odebrat", self)
            subtract_button.clicked.connect(self.create_subtract_button_function(row, quantity_input))
            self.table.setCellWidget(row, 8, subtract_button)  # Nastavíme tlačítko pro odebrání

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
            query = f"UPDATE produkty SET skladem = GREATEST(skladem - {quantity}, 0) WHERE id = {product_id}"

            # Provedení SQL dotazu
            with connection.cursor() as cursor:
                cursor.execute(query)
                connection.commit()

            # Aktualizace tabulky po změně
            self.update_table()

        return subtract_from_stock

if __name__ == "__main__":
    app = QApplication([])  # Vytvoření aplikace
    window = App()  # Vytvoření hlavního okna aplikace
    window.show()  # Zobrazení okna
    app.exec_()  # Spuštění aplikace
