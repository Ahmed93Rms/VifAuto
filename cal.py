valeurs = [
    300, 301, 302, 308, 310, 311, 312, 313, 314, 315, 316, 317, 320, 321, 322, 323,
    324, 325, 326, 327, 328, 329, 330, 331, 332, 341, 342, 343, 344, 345, 346, 351,
    352, 361, 362, 363, 364, 365, 366, 367, 368, 400, 401, 402, 403, 404, 405, 406,
    407, 408, 410, 415, 416, 420, 421, 422, 430, 440, 450, 451, 452, 453, 460, 470,
    471, 472, 480, 481, 495, 496, 500, 501, 502, 503, 504, 505, 822, 825
]

#for i in range(len(valeurs)):
   #print(f"INSERT INTO Produits VALUES ({i + 1}, {valeurs[i]}, 1000, "");")

qtt = [
    1600, 2900, 1680, 500, 390, 940, 780, 370, 500, 330,
    1000, 330, 860, 570, 1580, 850, 800, 1000, 500, 660,
    920, 1790, 520, 850, 1410, 680, 1000, 6100, 1000, 6200,
    290, 960, 1000, 3500, 920, 1000, 1000, 460, 5700, 980,
    2800, 3500, 3500, 3500, 1430, 1000, 3100, 6500, 880, 1730,
    690, 1260, 310, 1000, 1910, 800, 650, 450, 870, 710,
    800, 400, 590, 930, 1000, 870, 360, 740, 1000, 390,
    500, 500, 970, 500, 810, 420, 500, 650
]

for i in range(len(qtt)):
    print(f"INSERT INTO Quantite VALUES ({i + 1},0, {qtt[i]});")