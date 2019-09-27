# aufnahmekh-json
Gets the reception hospital in Linz from a public listing and formats it machine readable.

aufnahmekh-json is using a public listing from the austrian red cross to load the data.
It is really useful for example if you want to send automatic reminders for ambulance services with the hospital that accepts patients today.

The source of the data looks like this:

[![Public listing](https://i.imgur.com/H4Ttwux.png)](https://www.roteskreuz.at/ooe/dienststellen/perg/ichbrauchehilfe/aufnahmekrankenhaus/linz/)

# Installation
The installation is fairly simple. Just copy the aufnahmekh.php in your desired location and make sure it has write permission in it's folder.
The script will make a cache for the current day, this cache file is called aufnahmekh.json.

Now you are setup and ready to go!

# Example output
```json
[
  {
    "from_ts": 1569560400,
    "from_date": "27.09.2019 07:00",
    "to_ts": 1569646800,
    "to_date": "28.09.2019 07:00",
    "name": "Barmherzige Brüder Linz",
    "short_name": "BHB",
    "address": "Seilerstätte 2, 4021 Linz",
    "contact": "Telefon: 0732 / 7897- 0 "
  },
  {
    "from_ts": 1569560400,
    "from_date": "27.09.2019 07:00",
    "to_ts": 1569646800,
    "to_date": "28.09.2019 07:00",
    "name": "Elisabethinen Linz - Ordensklinikum",
    "short_name": "ELIS",
    "address": "Fadingerstraße 1, 4010 Linz",
    "contact": "Telefon: 0732 / 7676 - 0 "
  },
  {
    "from_ts": 1569646800,
    "from_date": "28.09.2019 07:00",
    "to_ts": 1569733200,
    "to_date": "29.09.2019 07:00",
    "name": "Barmherzige Schwestern Linz - Ordensklinikum",
    "short_name": "BHS",
    "address": "Seilerstätte 4, 4010 Linz",
    "contact": "Telefon: 0732 / 7677 - 0 "
  }
]
```
