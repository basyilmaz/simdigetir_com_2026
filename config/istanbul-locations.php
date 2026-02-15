<?php

/**
 * İstanbul İlçe ve Mahalle Veritabanı
 * SEO amaçlı lokal arama sayfaları için kullanılır
 * URL yapısı: /kurye/{ilce} ve /kurye/{ilce}/{mahalle}
 *
 * suffix: Türkçe bulunma hali eki (sesli ve sessiz uyumuna göre)
 *   Son ünlü a,ı,o,u → -da/-ta  |  Son ünlü e,i,ö,ü → -de/-te
 *   Son harf p,ç,t,k,f,h,s,ş → -ta/-te  |  Diğer → -da/-de
 */

return [

    // ===== AVRUPA YAKASI =====

    'kagithane' => [
        'name' => 'Kağıthane',
        'suffix' => "'de",
        'side' => 'avrupa',
        'lat' => 41.0882,
        'lng' => 29.0014,
        'neighborhoods' => [
            'yesilce' => 'Yeşilce',
            'cendere' => 'Cendere',
            'gultepe' => 'Gültepe',
            'merkez' => 'Merkez',
            'hamidiye' => 'Hamidiye',
            'seyrantepe' => 'Seyrantepe',
            'nurtepe' => 'Nurtepe',
            'harmantepe' => 'Harmantepe',
            'ortabayir' => 'Ortabayır',
            'talatpasa' => 'Talatpaşa',
        ],
    ],

    'sisli' => [
        'name' => 'Şişli',
        'suffix' => "'de",
        'side' => 'avrupa',
        'lat' => 41.0600,
        'lng' => 28.9872,
        'neighborhoods' => [
            'mecidiyekoy' => 'Mecidiyeköy',
            'nisantasi' => 'Nişantaşı',
            'osmanbey' => 'Osmanbey',
            'bomonti' => 'Bomonti',
            'fulya' => 'Fulya',
            'esentepe' => 'Esentepe',
            'gulbahar' => 'Gülbahar',
            'kurtulus' => 'Kurtuluş',
            'ferikoy' => 'Feriköy',
        ],
    ],

    'besiktas' => [
        'name' => 'Beşiktaş',
        'suffix' => "'ta",
        'side' => 'avrupa',
        'lat' => 41.0422,
        'lng' => 29.0069,
        'neighborhoods' => [
            'levent' => 'Levent',
            'etiler' => 'Etiler',
            'bebek' => 'Bebek',
            'ortakoy' => 'Ortaköy',
            'akatlar' => 'Akatlar',
            'ulus' => 'Ulus',
            'gayrettepe' => 'Gayrettepe',
            'dikilitas' => 'Dikilitaş',
            'arnavutkoy' => 'Arnavutköy',
            'kurucesme' => 'Kuruçeşme',
        ],
    ],

    'beyoglu' => [
        'name' => 'Beyoğlu',
        'suffix' => "'nda",
        'side' => 'avrupa',
        'lat' => 41.0370,
        'lng' => 28.9770,
        'neighborhoods' => [
            'karakoy' => 'Karaköy',
            'galata' => 'Galata',
            'cihangir' => 'Cihangir',
            'taksim' => 'Taksim',
            'istiklal' => 'İstiklal',
            'kasimpasa' => 'Kasımpaşa',
            'haskoy' => 'Hasköy',
            'findikli' => 'Fındıklı',
        ],
    ],

    'fatih' => [
        'name' => 'Fatih',
        'suffix' => "'te",
        'side' => 'avrupa',
        'lat' => 41.0186,
        'lng' => 28.9494,
        'neighborhoods' => [
            'sultanahmet' => 'Sultanahmet',
            'eminonu' => 'Eminönü',
            'sirkeci' => 'Sirkeci',
            'laleli' => 'Laleli',
            'aksaray' => 'Aksaray',
            'beyazit' => 'Beyazıt',
            'balat' => 'Balat',
            'fener' => 'Fener',
            'vefa' => 'Vefa',
            'capa' => 'Çapa',
        ],
    ],

    'bayrampasa' => [
        'name' => 'Bayrampaşa',
        'suffix' => "'da",
        'side' => 'avrupa',
        'lat' => 41.0458,
        'lng' => 28.9080,
        'neighborhoods' => [
            'yildirim' => 'Yıldırım',
            'muratpasa' => 'Muratpaşa',
            'kocatepe' => 'Kocatepe',
            'altintepsi' => 'Altıntepsi',
            'ismetpasa' => 'İsmetpaşa',
        ],
    ],

    'eyupsultan' => [
        'name' => 'Eyüpsultan',
        'suffix' => "'da",
        'side' => 'avrupa',
        'lat' => 41.0483,
        'lng' => 28.9341,
        'neighborhoods' => [
            'rami' => 'Rami',
            'alibeykoy' => 'Alibeyköy',
            'gokturk' => 'Göktürk',
            'kemerburgaz' => 'Kemerburgaz',
            'pirincci' => 'Pirinçci',
            'yesilpinar' => 'Yeşilpınar',
        ],
    ],

    'gaziosmanpasa' => [
        'name' => 'Gaziosmanpaşa',
        'suffix' => "'da",
        'side' => 'avrupa',
        'lat' => 41.0633,
        'lng' => 28.9125,
        'neighborhoods' => [
            'karayollari' => 'Karayolları',
            'barbaros' => 'Barbaros',
            'yildiztabya' => 'Yıldıztabya',
            'karlitepe' => 'Karlıtepe',
            'merkez' => 'Merkez',
        ],
    ],

    'sariyer' => [
        'name' => 'Sarıyer',
        'suffix' => "'de",
        'side' => 'avrupa',
        'lat' => 41.1582,
        'lng' => 29.0501,
        'neighborhoods' => [
            'maslak' => 'Maslak',
            'istinye' => 'İstinye',
            'tarabya' => 'Tarabya',
            'emirgan' => 'Emirgan',
            'rumelihisari' => 'Rumelihisarı',
            'baltalimani' => 'Baltalımanı',
            'ayazaga' => 'Ayazağa',
            'huzur' => 'Huzur',
        ],
    ],

    'bakirkoy' => [
        'name' => 'Bakırköy',
        'suffix' => "'de",
        'side' => 'avrupa',
        'lat' => 40.9819,
        'lng' => 28.8722,
        'neighborhoods' => [
            'yesilkoy' => 'Yeşilköy',
            'florya' => 'Florya',
            'atakoy' => 'Ataköy',
            'osmaniye' => 'Osmaniye',
            'zuhuratbaba' => 'Zuhuratbaba',
            'incirli' => 'İncirli',
        ],
    ],

    'bahcelievler' => [
        'name' => 'Bahçelievler',
        'suffix' => "'de",
        'side' => 'avrupa',
        'lat' => 41.0005,
        'lng' => 28.8614,
        'neighborhoods' => [
            'bahcelievler' => 'Bahçelievler',
            'siyavuspasa' => 'Siyavuşpaşa',
            'kocasinan' => 'Kocasinan',
            'soganli' => 'Soğanlı',
            'zafer' => 'Zafer',
            'yenibosna' => 'Yenibosna',
        ],
    ],

    'zeytinburnu' => [
        'name' => 'Zeytinburnu',
        'suffix' => "'nda",
        'side' => 'avrupa',
        'lat' => 41.0042,
        'lng' => 28.9077,
        'neighborhoods' => [
            'kazlicesme' => 'Kazlıçeşme',
            'merkezefendi' => 'Merkezefendi',
            'telsiz' => 'Telsiz',
            'maltepe' => 'Maltepe',
        ],
    ],

    'gungoren' => [
        'name' => 'Güngören',
        'suffix' => "'de",
        'side' => 'avrupa',
        'lat' => 41.0122,
        'lng' => 28.8836,
        'neighborhoods' => [
            'merkez' => 'Merkez',
            'gungoren' => 'Güngören',
            'tozkoparan' => 'Tozkoparan',
            'haznedar' => 'Haznedar',
        ],
    ],

    'kucukcekmece' => [
        'name' => 'Küçükçekmece',
        'suffix' => "'de",
        'side' => 'avrupa',
        'lat' => 41.0057,
        'lng' => 28.7750,
        'neighborhoods' => [
            'atakent' => 'Atakent',
            'halkali' => 'Halkalı',
            'sefakoy' => 'Sefaköy',
            'cennet' => 'Cennet',
            'kanarya' => 'Kanarya',
        ],
    ],

    'avcilar' => [
        'name' => 'Avcılar',
        'suffix' => "'da",
        'side' => 'avrupa',
        'lat' => 40.9793,
        'lng' => 28.7183,
        'neighborhoods' => [
            'firuzkoy' => 'Firuzköy',
            'mustafakemalpasa' => 'Mustafa Kemal Paşa',
            'denizkoskler' => 'Denizköşkler',
            'ambarli' => 'Ambarlı',
            'universiteler' => 'Üniversiteler',
        ],
    ],

    'esenyurt' => [
        'name' => 'Esenyurt',
        'suffix' => "'ta",
        'side' => 'avrupa',
        'lat' => 41.0331,
        'lng' => 28.6728,
        'neighborhoods' => [
            'merkez' => 'Merkez',
            'incigul' => 'İncigül',
            'ardicli' => 'Ardıçlı',
            'fatih' => 'Fatih',
        ],
    ],

    'beylikduzu' => [
        'name' => 'Beylikdüzü',
        'suffix' => "'nde",
        'side' => 'avrupa',
        'lat' => 41.0030,
        'lng' => 28.6370,
        'neighborhoods' => [
            'kavakli' => 'Kavaklı',
            'yakuplu' => 'Yakuplu',
            'adnan-kahveci' => 'Adnan Kahveci',
            'gurpinar' => 'Gürpınar',
        ],
    ],

    'basaksehir' => [
        'name' => 'Başakşehir',
        'suffix' => "'de",
        'side' => 'avrupa',
        'lat' => 41.0933,
        'lng' => 28.8028,
        'neighborhoods' => [
            'bahcesehir' => 'Bahçeşehir',
            'kayabasi' => 'Kayabaşı',
            'ikitelli' => 'İkitelli',
            'basaksehir' => 'Başakşehir',
        ],
    ],

    'arnavutkoy' => [
        'name' => 'Arnavutköy',
        'suffix' => "'de",
        'side' => 'avrupa',
        'lat' => 41.1844,
        'lng' => 28.7389,
        'neighborhoods' => [
            'hadimkoy' => 'Hadımköy',
            'tayakadin' => 'Tayakadın',
            'haracci' => 'Haraçcı',
        ],
    ],

    'catalca' => [
        'name' => 'Çatalca',
        'suffix' => "'da",
        'side' => 'avrupa',
        'lat' => 41.1433,
        'lng' => 28.4606,
        'neighborhoods' => [
            'merkez' => 'Merkez',
            'ferhatpasa' => 'Ferhatpaşa',
        ],
    ],

    'silivri' => [
        'name' => 'Silivri',
        'suffix' => "'de",
        'side' => 'avrupa',
        'lat' => 41.0731,
        'lng' => 28.2464,
        'neighborhoods' => [
            'merkez' => 'Merkez',
            'selimpasa' => 'Selimpaşa',
            'gumusyaka' => 'Gümüşyaka',
        ],
    ],

    'buyukcekmece' => [
        'name' => 'Büyükçekmece',
        'suffix' => "'de",
        'side' => 'avrupa',
        'lat' => 41.0204,
        'lng' => 28.5828,
        'neighborhoods' => [
            'merkez' => 'Merkez',
            'mimarsinan' => 'Mimar Sinan',
            'kumburgaz' => 'Kumburgaz',
        ],
    ],

    'bagcilar' => [
        'name' => 'Bağcılar',
        'suffix' => "'da",
        'side' => 'avrupa',
        'lat' => 41.0384,
        'lng' => 28.8566,
        'neighborhoods' => [
            'gunesli' => 'Güneşli',
            'mahmutbey' => 'Mahmutbey',
            'kirazli' => 'Kirazlı',
            'yildiztepe' => 'Yıldıztepe',
            'demirkapi' => 'Demirkapı',
            'fevzicakmak' => 'Fevzi Çakmak',
        ],
    ],

    'esenler' => [
        'name' => 'Esenler',
        'suffix' => "'de",
        'side' => 'avrupa',
        'lat' => 41.0436,
        'lng' => 28.8775,
        'neighborhoods' => [
            'davutpasa' => 'Davutpaşa',
            'menderes' => 'Menderes',
            'yildiz' => 'Yıldız',
            'havaalani' => 'Havaalanı',
        ],
    ],

    'sultangazi' => [
        'name' => 'Sultangazi',
        'suffix' => "'de",
        'side' => 'avrupa',
        'lat' => 41.1064,
        'lng' => 28.8669,
        'neighborhoods' => [
            'cebeci' => 'Cebeci',
            'esentepe' => 'Esentepe',
            'habibler' => 'Habibler',
            'sultanciftligi' => 'Sultançiftliği',
            'zubeydehanim' => 'Zübeyde Hanım',
            'yayla' => 'Yayla',
        ],
    ],

    // ===== ANADOLU YAKASI =====

    'kadikoy' => [
        'name' => 'Kadıköy',
        'suffix' => "'de",
        'side' => 'anadolu',
        'lat' => 40.9927,
        'lng' => 29.0277,
        'neighborhoods' => [
            'caferaga' => 'Caferağa',
            'moda' => 'Moda',
            'fenerbahce' => 'Fenerbahçe',
            'goztepe' => 'Göztepe',
            'bostanci' => 'Bostancı',
            'kozyatagi' => 'Kozyatağı',
            'suadiye' => 'Suadiye',
            'erenkoy' => 'Erenköy',
            'acibadem' => 'Acıbadem',
            'fikirtepe' => 'Fikirtepe',
        ],
    ],

    'uskudar' => [
        'name' => 'Üsküdar',
        'suffix' => "'da",
        'side' => 'anadolu',
        'lat' => 41.0266,
        'lng' => 29.0155,
        'neighborhoods' => [
            'cengelkoy' => 'Çengelköy',
            'beylerbeyi' => 'Beylerbeyi',
            'kuzguncuk' => 'Kuzguncuk',
            'altunizade' => 'Altunizade',
            'kisikli' => 'Kısıklı',
            'bulgurlu' => 'Bulgurlu',
            'acarkent' => 'Acarkent',
        ],
    ],

    'umraniye' => [
        'name' => 'Ümraniye',
        'suffix' => "'de",
        'side' => 'anadolu',
        'lat' => 41.0167,
        'lng' => 29.1194,
        'neighborhoods' => [
            'atakent' => 'Atakent',
            'adem-yavuz' => 'Adem Yavuz',
            'dudullu' => 'Dudullu',
            'ihlamurkuyu' => 'İhlamurkuyu',
            'yamanevler' => 'Yamanevler',
            'esenkent' => 'Esenkent',
        ],
    ],

    'atasehir' => [
        'name' => 'Ataşehir',
        'suffix' => "'de",
        'side' => 'anadolu',
        'lat' => 40.9833,
        'lng' => 29.1167,
        'neighborhoods' => [
            'atasehir' => 'Ataşehir',
            'kayisdagi' => 'Kayışdağı',
            'icerenkoy' => 'İçerenköy',
            'barbaros' => 'Barbaros',
            'yenisahra' => 'Yenisahra',
            'kucukbakkalkoy' => 'Küçükbakkalköy',
        ],
    ],

    'maltepe' => [
        'name' => 'Maltepe',
        'suffix' => "'de",
        'side' => 'anadolu',
        'lat' => 40.9347,
        'lng' => 29.1322,
        'neighborhoods' => [
            'cevizli' => 'Cevizli',
            'altaycesme' => 'Altayçeşme',
            'girne' => 'Girne',
            'baglarbasi' => 'Bağlarbaşı',
            'findikli' => 'Fındıklı',
            'idealtepe' => 'İdealtepe',
        ],
    ],

    'kartal' => [
        'name' => 'Kartal',
        'suffix' => "'da",
        'side' => 'anadolu',
        'lat' => 40.8903,
        'lng' => 29.1881,
        'neighborhoods' => [
            'soganlik' => 'Soğanlık',
            'kordonboyu' => 'Kordonboyu',
            'yakacik' => 'Yakacık',
            'ugurmumcu' => 'Uğur Mumcu',
            'huzur' => 'Huzur',
        ],
    ],

    'pendik' => [
        'name' => 'Pendik',
        'suffix' => "'te",
        'side' => 'anadolu',
        'lat' => 40.8761,
        'lng' => 29.2556,
        'neighborhoods' => [
            'kaynarca' => 'Kaynarca',
            'yenisehir' => 'Yenişehir',
            'kurtkoy' => 'Kurtköy',
            'velibaba' => 'Velibaba',
            'guzelyali' => 'Güzelyalı',
        ],
    ],

    'tuzla' => [
        'name' => 'Tuzla',
        'suffix' => "'da",
        'side' => 'anadolu',
        'lat' => 40.8167,
        'lng' => 29.3000,
        'neighborhoods' => [
            'aydinli' => 'Aydınlı',
            'icmeler' => 'İçmeler',
            'postane' => 'Postane',
            'merkez' => 'Merkez',
        ],
    ],

    'sancaktepe' => [
        'name' => 'Sancaktepe',
        'suffix' => "'de",
        'side' => 'anadolu',
        'lat' => 41.0022,
        'lng' => 29.2272,
        'neighborhoods' => [
            'samandira' => 'Samandıra',
            'sarigazi' => 'Sarıgazi',
            'yenidogan' => 'Yenidoğan',
            'osmangazi' => 'Osmangazi',
        ],
    ],

    'cekmekoy' => [
        'name' => 'Çekmeköy',
        'suffix' => "'de",
        'side' => 'anadolu',
        'lat' => 41.0333,
        'lng' => 29.1833,
        'neighborhoods' => [
            'merkez' => 'Merkez',
            'alemdag' => 'Alemdağ',
            'tasdelen' => 'Taşdelen',
            'omerli' => 'Ömerli',
        ],
    ],

    'beykoz' => [
        'name' => 'Beykoz',
        'suffix' => "'da",
        'side' => 'anadolu',
        'lat' => 41.1267,
        'lng' => 29.0961,
        'neighborhoods' => [
            'kavacik' => 'Kavacık',
            'anadoluhisari' => 'Anadoluhisarı',
            'cubuklu' => 'Çubuklu',
            'pasabahce' => 'Paşabahçe',
            'riva' => 'Riva',
        ],
    ],

    'sile' => [
        'name' => 'Şile',
        'suffix' => "'de",
        'side' => 'anadolu',
        'lat' => 41.1778,
        'lng' => 29.6128,
        'neighborhoods' => [
            'merkez' => 'Merkez',
            'agva' => 'Ağva',
        ],
    ],

    'adalar' => [
        'name' => 'Adalar',
        'suffix' => "'da",
        'side' => 'anadolu',
        'lat' => 40.8756,
        'lng' => 29.0906,
        'neighborhoods' => [
            'buyukada' => 'Büyükada',
            'heybeliada' => 'Heybeliada',
            'burgazada' => 'Burgazada',
            'kinaliada' => 'Kınalıada',
        ],
    ],

    'sultanbeyli' => [
        'name' => 'Sultanbeyli',
        'suffix' => "'de",
        'side' => 'anadolu',
        'lat' => 40.9628,
        'lng' => 29.2678,
        'neighborhoods' => [
            'merkez' => 'Merkez',
            'hasanpasa' => 'Hasanpaşa',
            'orhangazi' => 'Orhangazi',
        ],
    ],

];
