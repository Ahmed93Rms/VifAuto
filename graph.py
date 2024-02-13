import matplotlib.pyplot as plt
import matplotlib.dates as mdates
from datetime import datetime

# Exemple de données
donnees = [
    {"idP": 1, "valeur": 10, "date": "2023-01-01"},
    {"idP": 1, "valeur": 15, "date": "2023-01-02"},
    {"idP": 2, "valeur": 7, "date": "2023-01-01"},
    {"idP": 1, "valeur": 12, "date": "2023-01-03"},
    {"idP": 2, "valeur": 12, "date": "2023-02-03"},
    {"idP": 1, "valeur": 14, "date": "2023-02-03"},
    {"idP": 2, "valeur": 12, "date": "2023-03-03"},
]

idP_selectionne = 2

donnees_filtrees = [d for d in donnees if d["idP"] == idP_selectionne]

dates = [datetime.strptime(d["date"], "%Y-%m-%d") for d in donnees_filtrees]
valeurs = [d["valeur"] for d in donnees_filtrees]

plt.figure(figsize=(10, 6))
plt.plot(dates, valeurs, marker='o', linestyle='-', color='black')

plt.gca().xaxis.set_major_formatter(mdates.DateFormatter('%m-%d'))
plt.gca().xaxis.set_major_locator(mdates.DayLocator())

plt.gcf().autofmt_xdate()

plt.title(f'Quantité utilisé pour le produit {idP_selectionne}')
plt.xlabel('Date')
plt.ylabel('Valeur')
plt.grid(False)

plt.show()