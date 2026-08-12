<?php

namespace Tests\Helpers;

use App\Helpers\CadasterNumber;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CadasterNumberTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testResolve()
    {
        $resolver = new CadasterNumber();

        $this->assertEquals(false, $resolver->resolve('KNC 999, Nižná Šebastová'));
        $this->assertEquals('3456', $resolver->resolve('KNC 999, k. ú.: Nižná Šebastová'));
        $this->assertEquals('3456',
            $resolver->resolve('zemku parc. reg. „C“ č. 368, v kat. území Nižná Šebastová, Hlohove'));
        $this->assertEquals('3456', $resolver->resolve('LV Č. 6492, k. Ú. Nižná Šebastová'));
        $this->assertEquals('3456', $resolver->resolve('LV Č. 6492, k.  Ú. Nižná Šebastová'));
        $this->assertEquals('3456', $resolver->resolve('KNC 999, katastrálne územie Nižná Šebastová'));
        $this->assertEquals('3456', $resolver->resolve('KNC 999, kat. územie Nižná Šebastová'));
        $this->assertEquals('3456', $resolver->resolve('KNC 999, kat. úz. Nižná Šebastová'));
        $this->assertEquals('2185', $resolver->resolve('parc. č. KNC 571/1, KNC 571/2, KNC 568/2, KNC 711,
KNC 9677/1, KNC 9678 vk. ú. Prešov.'));
        $this->assertEquals('2185', $resolver->resolve('ister "C" parc. č. 532/1, 543/1 v katastrálnom území
Prešov navrhovateľa'));
        $this->assertEquals('2185', $resolver->resolve('-pozemku parc. č. KNE 233112, zastavaná plocha a nádvorie o výmere 119 m2, zapísaný na 
L V č. 6492, k. ú. Prešov, lokalita Ul. Tehelná pre SANKUR- NÁBREŽNÁ, s.r.o., Kmet\'ovo 
stromoradie 9/B, 080 Ol Prešov, IČO: Sl 198 151.'));

    }

    public function testResolveParcelsNearOccurence()
    {
        \App\CadasterNumber::create([
            'id' => 12345,
            'city' => 'Považský Chlmec'
        ]);

        $text = "(ďalej len \"stavba\") na pozemku register \"C\" parc. č. 1345/1 (objekt), 1345/28, 1345/29

(prípojky IS) v katastrálnom území Považský Chlmec.

Doba trvania stavby: dočasná - do doby platnosti nájomnej zmluvy uzatvorenej medzi";

        $resolver = new CadasterNumber();
        $number = $resolver->resolve($text);

        $this->assertEquals(12345, $number);
        $this->assertEquals([
            '1345/1',
            '1345/28',
            '1345/29',
        ], $resolver->parcels);

    }

    public function testResolveParcelsNearOccurence2()
    {
        \App\CadasterNumber::create([
            'id' => 12345,
            'city' => 'Senica'
        ]);

        $text = "Kraj: Banská Bystrica, okres: Banská Bystrica, 
účel vyvlastnenia: Rýchlostná cesta „R1 Banská Bystrica — Slovenská Ľupča“ 
vyvlastniteľ: Národná diaľničná spoločnosť a.s., Dúbravská cesta 14, 841 04 Bratislava 
katastrálne územie: Senica 
parcelné číslo pozemkov: KNC: 753/7, 753/8, 753/9, 753/10, 753/11, 753/12, 753/14, 753/16, 
753/18: 
Podľa ust. $ 10 ods.2 a 3 zákona o vyvlastňovaní informácie Zverejní na svojej úradnej tabuli 
ana svojom webovom sídle obec, ak ho má zriadené, v ktorej katastrálnom území sa ";

        $resolver = new CadasterNumber();
        $number = $resolver->resolve($text);

        $this->assertEquals(12345, $number);
        $this->assertEquals([
            '753/7',
            '753/8',
            '753/9',
            '753/10',
            '753/11',
            '753/12',
            '753/14',
            '753/16',
            '753/18',
        ], array_values($resolver->parcels));

    }

    public function testResolveParcelsNearOccurence1()
    {
        \App\CadasterNumber::create([
            'id' => 12345,
//            'city' => 'Považský Chlmec'
            'city' => 'Budatín'
        ]);

        $text = '166
MESTO ŽILINA
Stavebný úrad ,
Námestie obetí komunizmu č. 1, 011 31 Zilina
Č.s.: 6502/2020-153763/2020-SU-PA Zilina: 12.06.2020
STAVEBNÉ POVOLENIE

Mesto Žilina - Stavebný úrad, (ďalej len "stavebný úrad") ako stavebný úrad príslušný podľa

8 117 ods. 1 zákona č. 50/1976 Zb. o územnom plánovaní a stavebnom poriadku (stavebný

zákon) v znení neskorších predpisov (ďalej len "stavebný zákon") a zákona č. 416/2001 Z.z. o

prechode niektorých pôsobností z orgánov štátnej správy na obce a vyššie územné celky

v znení neskorších predpisov, v stavebnom konaní preskúmal podľa $ 62 stavebného zákona

žiadosť o stavebné povolenie, ktorú dňa 12.03.2019 podal a dňa 06.11.2019 doplnil

Miroslav Takáč, Radlinského 296/3, 010 01 Žilina
(ďalej len "stavebník"), a na základe tohto preskúmania vydáva podľa $ 66 stavebného
zákona
stavebné povolenie

na stavbu

"Rodinný dom s dvoma bytovými jednotkami, prípojky inžinierskych sietí, oporné
múry"
Žilina - Budatín, ul. Tyrkysová

(ďalej len "stavba") na pozemku register "C" parc. č. 712/423 v katastrálnom území Budatín.

Stavba obsahuje:

- Novostavba rodinného dvojdomu je navrhnutá v obytnom súbore OS IBV 35 RD Žilina -
Budatín - Pod komunikáciou, ul. Tyrkysová. Navrhovaná stavba je uvažovaná ako
jednopodlažný samostatne stojaci objekt, ktorý je čiastočne podpivničený. Zastrešený
bude pultovou strechou. Max. pôdorysné rozmery objektu sú 11,0m x 16,0m, výška
stavby + 4,387m od + 0,000 (podlaha 1.NP), výška komína + 5,10m.

- “Na pozemku budú vytvorené tri parkovacie státia pre osobné motorové vozidlá na
spevnenej ploche, z toho jedno bude slúžiť pre obytnú jednotku č.1 a dve pre obytnú
jednotku č.2. Druhé parkovacie státie pre obytnú jednotku č.1 bude v garáži, ktorá je
súčasť ou tejto obytnej jednotky.

- Obytná jednotka č.1: do domu sa vchádza cez zádverie 1.PP, ktoré je prepojené s garážou
a chodbou. Cez chodbu sa vchádza do hosťovskej izby a kúpeľne, ktorá má tiež funkciu
technickej miestnosti. Na poschodí (1.NP) je priestor rozdelený na dennú a nočnú časť,
ktoré sú spojené chodbou. V nočnej časti sa nachádza spálňa rodičov, samostatné WC,
izba a kúpeľňa. Dennú časť tvorí otvorený priestor, v ktorom bude kuchyňa, jedáleň
a obývacia izba. Z jedálne je vstup na terasu.

- Obytná jednotka č.2: do domu sa vchádza cez zádverie a z neho sa prechádza do chodby.
Chodba spája sklad, ktorý sa bude využívať aj ako práčovňa a technickú miestnosť, ktorá
má aj vlastný vchod z exteriéru. Na poschodí (1.NP) je priestor rozdelený na dennú
Č.s.: 6502/2020-153763/2020-SU-PA str. 2
a nočnú časť, ktoré sú spojené chodbou. V nočnej časti sa nachádza izba a kúpeľňa.
Dennú časť tvorí otvorený priestor, v ktorom bude kuchynský kút a obývacia izba,
z ktorej je vstup na terasu.

- Vetranie rodinného dvojdomu bude zabezpečovať rekuperácia a mikrovetranie
v okenných konštrukciách. Vykurovanie a ohrev teplej vody oboch obytných jednotiek
bude zabezpečené samostatnými plynovými kondenzačnými kotlami. Všetky miestnosti
budú mať podlahové teplovodné vykurovanie.

- Objekt dvojdomu bude napojený na inžinierske siete samostatnými prípojkami pre každú
obytnú jednotku - vodovodná, splaškovej kanalizácie, plynová a elektrická NN. Dažďová
voda zo strechy objektu bude zvedená do navrhovanej spoločnej vsakovacej šachty na
pozemku investora.

- Navrhnuté sú dva oporné múry v tvare písmena „L“ s celkovou dížkou 26,60m. Výškovo
sú členené podľa sklonu terénu, najvyššia výška je cca 2,75m nad terénom. Každý oporný
múr je navrhnutý z debniacich tvárnic šírky 300mm, základ bude železobetónový,
monolitický šírky 650mm a výšky 1000mm. Rubová strana múru bude izolovaná proti
zemnej vlhkosti hydroizoláciou. Za hydroizoláciou je ochranná vrstva s drenážnou
funkciou. Drenáž bude zaústená do vsakovacej šachty.

Pre uskutočnenie stavby sa určujú tieto podmienky:

1. Podmienky určené v zmysle $ 66 ods. 3 stavebného zákona:

a) stavba bude umiestnená na pozemku register "C" parc. č. 712/423 v katastrálnom
území Budatín, podľa podmienok určených územným rozhodnutím č. 2863/2018-
30263/2018-OSP-PA zo dňa 22.05.2018, právoplatného dňa 07.07.2018,

b) stavba bude uskutočnená podľa projektovej dokumentácie overenej v stavebnom
konaní, ktorú vypracoval autorizovaný stavebný inžinier Ing. Václav First (č. reg.
2451+A1), Ateliér FS, s.r.o., Hviezdoslavova 106, 026 01 Vyšný Kubín a ktorá je
prílohou tohto rozhodnutia, prípadné zmeny nesmú byť vykonané bez
predchádzajúceho povolenia stavebného úradu,

c) dodržiavať predpisy týkajúce sa bezpečnosti a ochrany zdravia pri práci, a ochrany
životného prostredia,

d) stavbu realizovať v súlade so všeobecnými technickými požiadavkami na výstavbu
podľa $ 43e stavebného zákona, v súlade so všeobecnými technickými požiadavkami
na uskutočňovanie stavieb podľa 8 48 až 53 stavebného zákona, av súlade s
príslušnými technickými normami STN a EN, zvlášť normami protipožiarnymi,
bezpečnostnými a hygienickými,

e) stavebný úrad určuje lehotu na dokončenie stavby - stavba bude ukončená najneskôr
do 07/2023 (mesiac arok) a po ukončení je potrebné požiadať o vydanie
kolaudačného rozhodnutia,

f) plniť požiadavky v záväzných stanoviskách podľa $ 140b stavebného zákona
uplatnené dotknutými orgánmi, ak nie sú určené správnymi rozhodnutiami, prípadne
požiadavky vlastníkov sietí a zariadení verejného dopravného technického vybavenia
na napojenie na tieto siete,

£) na uskutočňovanie stavby použiť iba vhodné stavebné výrobky, ktoré sú vhodné na
použitie v stavbe na zamýšľaný účel (podľa zákona č. 133/2013 Z.z. o stavebných
výrobkoch),

h) začatie stavby písomne oznámiť na stavebný úrad do 5 dní.

2. Vyjadrenia, stanoviská a požiadavky určené dotknutými orgánmi:
Č.s.: 6502/2020-153763/2020-SU-PA str. 3
e Okresný úrad Žilina, OSŽP, oddelenie ochrany prírody vybraných zložiek
životného prostredia na úseku EIA - záväzné stanovisko k PD pre stavebné povolenie
č. OU-ZA-OSZP3-2020/021504-002/Hnl zo dňa 29.05.2020 - porovnanie údajov
stanovených v rozhodnutí zo zisťovacieho konania Okresného úradu Žilina, OSŽP č. OU-
ZA-OSZP3-2016/019919-003/Hnl zo dňa 15.08.2016 pre navrhovanú činnosť „OS IBV
35 RD Žilina - Budatín - Pod komunikáciou“ s údajmi uvedenými v podkladoch pre
stavebné povolenie, je možné konštatovať, že projektová dokumentácia pre stavbu
„Rodinný dom s dvoma bytovými jednotkami, prípojky inžinierskych sietí, oporné múry“
Žilina - Budatín, ul. Tyrkysová je v súlade so zákonom o posudzovaní vplyvov na
životné prostredie a rozhodnutím zo zisťovacieho konania vydaným Okresným
úradom Žilina, OSŽP, oddelenie ochrany prírody vybraných zložiek ŽP pod č. OU-ZA-

OSZP3-2016/019919-003/Hnl zo dňa 15.08.2016.

e OÚ Žilina, OSŽP, štátna správa ochrany prírody a krajiny - v zmysle vyjadrenia pre
účely stavebného konania č. OU-ZA-OSZP3-2019/044131-002/Bal zo dňa 31.10.2019 :
Stavba sa bude realizovať v k.ú. Budatín, extravilán obce, parcely č. KN-C 712/423,
712/424 sú podľa katastra nehnuteľnosti vedené ako ostatná plocha a orná pôda. Na
predmetné územie sa vzťahuje prvý stupeň ochrany v zmysle zákona č. 543/2002 Z. z. o
ochrane prírody a krajiny v znení neskorších predpisov.

Okresný úrad Žilina, odbor starostlivosti o životné prostredie, oddelenie ochrany prírody

a vybraných zložiek životného prostredia, štátna správa ochrany prírody a krajiny z

hľadiska záujmov ochrany prírody a krajiny, nemá námietky k vydaniu stavebného

povolenia na predmetnú stavbu za predpokladu akceptovania stanovených podmienok :

1. Prípadný nevyhnutný a opodstatnený výrub drevín a krovín bude riešený v zmysle $
47 a 8 48 zákona (t. j. na dreviny a kroviny, ktoré na danom pozemku rastú a ktorých
výrub podlieha súhlasu orgánu ochrany prírody, bude vydané právoplatné
rozhodnutie o súhlase na výrub drevín a krovín v správnom konaní).

2. Pti realizácii výkopových prác bude výkop zasypaný v ten istý deň, ako dôjde k
vyhíbeniu výkopu tak, aby neostal ako pasca pre živočíchy cez noc.

- pokiaľ nebude možné v deň realizácie výkopu tento zasypať, bude zabezpečené,
aby do priestoru výkopu nemohli preniknúť živočíchy, vrátane živočíchov
veľkosti drobných zemných cicavcov, obojživelníkov a pod.).

3. Výkopová zemina bude využitá na spätný zához alebo odvezená na určenú skládku,
v žiadnom prípade nebude deponovaná v blízkosti vodných tokov a nebude
umiestňovaná na nelesné plochy mimo intravilánu.

4. Okolie dotknuté stavebnou činnosťou bude každoročne sledované a udržiavané
vlastníkom pozemku tak, aby nevznikali plochy ruderálnej vegetácie, resp., aby sa
zabránilo výskytu nepôvodných inváznych rastlín (napr. Fallopia japonica -
pohánkovej japonský, Heracleum mantegazzianum - boľševník obrovský a. 1.).

5. Pri plánovanej realizácii výsadby bylín, krov a drevín odporúčame uprednostniť
geograficky pôvodné druhy.

e Slovak Telekom a.s. a DIGI SLOVAKIA, s.r.o. - v zmysle vyjadrenia č.: 6611930664
zo dňa 03.11.2019 - nedôjde do styku so sieťami elektronických komunikácii (ďalej len
SEK) spoločnosti Slovak Telekom, a.s. a/alebo DIGI SLOVAKIA, s.r.o.

1. Existujúce zariadenia sú chránené ochranným pásmom ($68 zákona č. 351/2011 Z. z.)
a zároveň je potrebné dodržať ustanovenie $65 zákona č. 351/2011 2. z. o ochrane
proti rušeniu.

2. Vyjadrenie stráca platnosť uplynutím doby platnosti uvedenej vyššie vo vyjadrení,
v prípade zmeny vyznačeného polygónu, dôvodu žiadosti, účelu žiadosti, v prípade ak
uvedené parcelné číslo v žiadosti nezodpovedá vyznačenému polygónu alebo ak si
Č.s.: 6502/2020-153763/2020-SU-PA str. 4
stavebník nesplní povinnosť podľa bodu 3.

3. Stavebník alebo ním poverená osoba je povinná v prípade ak zistil, že jeho zámer, pre
ktorý podal uvedenú žiadosť je v kolízii so SEK Slovak Telekom a.s. a/alebo DIGI
SLOVAKIA, s.r.o. alebo zasahuje do ochranného pásma týchto sieti (najneskôr pred
spracovaním projektovej dokumentácie stavby), vyzvať spoločnosť Slovak Telekom,
a.s. na stanovenie konkrétnych podmienok ochrany alebo preloženia SEK
prostredníctvom zamestnanca spoločnosti povereného správou sietí.

4. V prípade ak sa na Vami definovanom území v žiadosti o vyjadrenie sa nachádza
nadzemná telekomunikačná sieť, ktorá je vo vlastníctve Slovak Telekom a.s. a/alebo
DIGI SLOVAKIA, s.r.o., je potrebné zo strany žiadateľa zabezpečiť nadzemnú sieť
proti poškodeniu, alebo narušeniu ochranného pásma.

5. Nedodržanie vyššie uvedených podmienok ochrany zariadení je porušením povinností
podľa $ 68 zákona č. 351/2011 Z.z. o elektronických komunikáciách v platnom znení.

6. Žiadateľ môže vyjadrenie použiť iba pre účel, pre ktorý mu bolo vystavené. Okrem
použitia na účel konaní podľa stavebného zákona a následnej realizácie výstavby,
žiadateľ nie je oprávnený poskytnuté informácie a dáta ďalej rozširovať, prenajímať
alebo využívať bez súhlasu spoločnosti Slovak Telekom a.s.

7. Žiadateľa zároveň upozorňujeme, že v prípade ak plánuje napojiť nehnuteľnosť na
elektronickú komunikačnú sieť úložným vedením, je potrebné do projektu pre územné
rozhodnutie doplniť aj telekomunikačnú prípojku.

8. Prílohy k vyjadreniu :

- Situačný výkres obsahujúci záujmové územie žiadateľa.

e Krajský pamiatkový úrad Žilina vzmysle vyjadrenia č. KPUZA-2018/6303-
2/20456/K OP zo dňa 19.03.2018:

- ak počas stavebných prác dôjde k akémukoľvek archeologickému nálezu hmotnej
povahy, napr. objektu, kultúrnej vrstvy, zvyškov starších architektúr, stavebného
materiálu, hrobov, keramiky, pracovných nástrojov, mincí alebo kostrových
pozostatkov, je nevyhnutné nález okamžite ohlásiť Krajskému pamiatkovému úradu
Žilina, najneskôr na druhý pracovný deň po nájdení a ponechať ho bez zmeny až do
obhliadky krajským pamiatkovým úradom.

- Zabezpečiť preukázateľným spôsobom oboznámenie s podmienkami záväzného
stanoviska všetky subjekty zúčastňujúce sa na realizácii stavby.

e OR HaZZ v Žiline, stanovisko na účely stavebného konania č. ORHZ-ZA1-1668-
001/2017 zo dňa 06.12.2017 - s riešením protipožiarnej bezpečnosti stavby súhlasí bez
pripomienok.

Toto stanovisko nenahrádza stanovisko orgánu štátneho požiarneho dozoru pre konanie

nasledujúce podľa zákona č. 50/1976 Zb. o územnom plánovaní a stavebnom poriadku

(stavebný zákon) v znení neskorších predpisov a spolu s nami overenou projektovou

dokumentáciou ho požadujeme predložiť pri kolaudačnom konaní.

e Mesto Žilina, Odbor životného prostredia - súhlas na povolenie stavby malého zdroja
znečisťovania ovzdušia č. 3624/2017-7987/2017-OŽP-GAB zo dňa 22.02.2017. Na
vykurovanie rodinného dvojdomu a prípravu teplej úžitkovej vody sú navrhnuté 2 kusy
plynových kondenzačných kotlov, pre každú bytovú jednotku samostatne jeden kotol.
Každý z kotlov bude umiestnený v technickej miestnosti v suteréne bytovej jednotky,
odvod spalín bude riešený od každého kotla samostatným komínom vyvedeným nad
strechu objektu. Doplnkovým zdrojom tepla v každej bytovej jednotke bude krbová
vložka na pevné palivo, ktorá bude umiestnená v obývacej miestnosti. Odvod spalín od
každého krbu bude zabezpečený samostatným komínom vyvedeným nad strechu.
Č.s.: 6502/2020-153763/2020-SU-PA str. 5
Vydáva súhlas s nasledovnými podmienkami:
1. Odvod spalín od kotlov musí byť riešený v súlade s vyhláškou č. 410/2012 Z.z.,
ktorou sa vykonávajú niektoré ustanovenia zákona o ovzduší (príloha č. 9, ods. 5).
2. Prípadné zmeny o type a výkone zdroja je potrebné predložiť tunajšiemu úradu na
schválenie.
3. Ako vykurovacie médium je možné používať len výrobcom predpísané.
4. Prevádzkovateľ malého zdroja znečisťovania ovzdušia je povinný dodržiavať platnú
právnu legislatívu v oblasti ochrany ovzdušia, povinnosti uvedené v $ 16 zákona
o ovzduší.
S. Pred vydaním kolaudačného rozhodnutia je potrebné požiadať tunajší úrad
o vydanie súhlasu na užívanie stavby malých zdrojov znečisťovania ovzdušia.
3. Podmienky určené v zmysle $ 66 ods. 4 stavebného zákona:
a) zabezpečiť komplexnosť a plynulosť uskutočnenia stavby podľa POV.
b) napojenie na siete a zariadenia technického vybavenia :

- vodovodná prípojka: v rámci výstavby vodovodu v miestnej komunikácii bola
vybudovaná verejná časť vodovodnej prípojky s vodomernou šachtou na pozemku
investora. Z vodomernej šachty budú napojené dve samostatné vodovodné prípojky
pre obytnú jednotku č.1 a obytnú jednotku č.2, so samostatným meraním v exist. VŠ.

- kanalizačná prípojka: vrámci výstavby splaškovej kanalizácie v miestnej
komunikácii bola vybudovaná kanalizačná prípojka z mat. PVC DN150 s revíznou
kanalizačnou šachtou DN400 na pozemku investora. Obytné jednotky č.1, 2 budú
napojené spoločnou navrhovanou prípojkou splaškovej kanalizácie, ktorá sa zaústi
do exist. šachty.

V zmysle vyjadrenia SEVAK a.s. č. 017018577 KMi/KLi zo dňa 18.08.2017 - k PD

pre ÚR a SP - súhlasné stanovisko:

1. S vydaním územného rozhodnutia na stavbu „Novostavba rodinného dvojdomu“
parc. č. 712/423, 712/424, k. ú. Budatín súhlasíme. Cez uvedené parcely
neprechádzajú inžinierske siete v správe SEVAK. V rámci stavby vodovodu a
splaškovej kanalizácie boli vybudované časti prípojok pre parc. č. 712/423.

S vydaním stavebného povolenia na stavbu „Novostavba rodinného dvojdomu“ parc.

č. 712/423, 712/424, k.ú. Budatín súhlasíme za dodržania nižšie uvedených

podmienok :

1. Ostré napojenie na vodovod a splaškovú kanalizáciu bude možné až po doriešení
vlastníckych vzťahov medzi investorom stavby vodovodu a kanalizácie a našou
spoločnosťou.

2. Na meranie spotreby vody bude osadené meradlo On — 2,5 m“/hod DN 20. Tento
fakturačný vodomer dodá naša spoločnosť. Vodomer bude osadený vo
vodomernej zostave na ráme typ SEVAK, a. s.

3. Nákup, prevádzku druhého vodomeru a rozúčtovanie vodného a stočného si
zabezpečí investor stavby. Tento podružný vodomer bude osadený za
navrhovaným fakturačným meradlom.

4. Do splaškovej kanalizácie DN 300 zaústiť len splaškové O.V.. Kvalita
vypúšťaných odpadových vôd musí zodpovedať stanoveným limitom znečistenia
uvedených v kanalizačnom poriadku VK Žilina.

5. V prípade odkanalizovania priestorov suterénu nehnuteľností resp. priestorov
umiestnených pod úrovňou poklopov na VK je nutné zabezpečiť v zmysle STN-
EN 13564-1 tieto priestory vhodným zariadením zabraňujúcim zaplaveniu z
kanalizačných potrubí (napr. spätné klapky).
Č.s.: 6502/2020-153763/2020-SU-PA str. 6
6. Fakturácia vodného a stočného bude vykonávaná na základe osadeného hlavného
fakturačného meradla v správe a majetku SEVAK a.s. Žilina od dátumu realizácie

prípojok.

7. Upozorňujeme na skutočnosť, že predmetná kanalizácia, do ktorej bude
nehnuteľnosť napojená kanalizačnou prípojkou nie majetkom SEVAK a. s. a
SEVAK a. s. nie je ani jej prevádzkovateľom. Preto Vám ako bod zmluvného
plnenia určujeme revíznu šachtu Š29 na kanalizačnom zberači Zádubnie -
Budatín, do ktorej je kanalizácia vyústená cez ďalší úsek kanalizácie, ktorý takisto
nie je v majetku SEVAK a. s. a ani ho naša spoločnosť neprevádzkuje. Aby ste sa
vyhli neskorším problémom s odvádzaním odpadových vôd z Vašej nehnuteľnosti
odporúčame Vám zabezpečiť si súhlasy na pripojenie KP od majiteľa úseku
kanalizácie, do ktorého bude KP zaústená a na odvádzanie OV od majiteľov
obidvoch úsekov kanalizácie, cez ktoré budete odpadové vody z Vašej
nehnuteľnosti do verejnej kanalizácie odvádzať.

8. Samotné napojenie na verejný vodovod a splaškovú kanalizáciu je podmienené
vybavením Žiadosti o zriadenie vodovodnej prípojky a Žiadosti o zriadenie
kanalizačnej prípojky a podpisom zmluvy na dodávku pitnej vody a odvedenie
splaškových odpadových vôd. Tlačivá a potrebné informácie pre zriadenie VP a
KP poskytne príslušný zamestnanec SEVAK a.s., na Zákazníckom centre.

9. Stavebník pred zahájením zemných prác požiada o vytýčenie podzemných vedení,
ktoré sa nachádzajú v záujmovom území a zabezpečí, aby pri realizácii VP a KP
bola dodržaná STN 73 6005 pri súbehu a križovaní VP a KP s ostatnými
vedeniami.

10. Pred zásypom vodovodnej a kanalizačnej prípojky požadujeme prizvať našich
pracovníkov ku kontrole stavu vykonaných prác, ako aj k spôsobu napojenia
vodovodnej prípojky a k spôsobu zaústenia kanalizačnej prípojky a umiestnenia
vodomernej a kanalizačnej šachty. Celá kanalizačná prípojka musí byť
vybudovaná ako vodotesná. Ku skúškam vodotesnosti (vykonať podľa STN EN
1610) a tlakovej skúške vodovodnej prípojky (vykonať podľa STN EN 805) je
potrebné prizvať našich pracovníkov.

11. V zmysle zákona č. 442/2002 Z.z. o verejných vodovodoch a kanalizáciách
vlastník vodovodnej a kanalizačnej prípojky je povinný zabezpečiť opravy a
údržbu vodovodnej a kanalizačnej prípojky na vlastné náklady.

- elektrická prípojka: v rámci výstavby NN rozvodov v miestnej komunikácii bola
vybudovaná verejná časť prípojky NN selektromerovou skriňou na pozemku
stavebníka. Zo skrine budú vedené samostatné elektrické NN prípojky pre jednotlivé
obytné jednotky. Každá obytná jednotka bude mať samostatný domový rozvádzač.

V zmysle vyjadrenia Stredoslovenská distribučná a.s., č. 4300061079/52 (Rodinný

dom č.1) ač. 4300072527/53 (Rodinný dom č.2) zo dňa 14.02.2019 - identické

vyjadrenia k územnému rozhodnutiu a k stavebnému povoleniu v znení :

Toto vyjadrenie slúži aj pre účely územného rozhodnutia, resp. stavebného povolenia

pri splnení "Všeobecných podmienok k vyhotoveniu stavby a k pripojeniu odberných

miest do distribučnej sústavy spoločnosti Stredoslovenská distribučná, a.s. (ďalej

SSD)", ktoré sú zverejnené na stránkach www.ssd.sk .

Stredoslovenská distribučná, a.s. ( ďalej len SSD ) odporúča, aby žiadateľ predložil

toto vyjadrenie vybratej elektroinštalačnej firme, ktorá bude realizovať prípravu

daného odberného miesta, nakoľko časť informácii v tomto vyjadrení musí byť a je
popísaná v odbornej elektrotechnickej terminológii, ktorej laik nemusí rozumieť,
pričom odborne spôsobilá osoba vie ako má postupovať.
Č.s.: 6502/2020-153763/2020-SU-PA str. 7

SSD s hore uvedenou stavbou súhlasí s nasledovnými pripomienkami:

1. V predmetnej lokalite katastra Žilina - Budatín, parcela č. 712/423, 424, resp. v jej
blízkosti sa nachádzajú podzemné NN vedenia a skrine. Od uvedených
energetických zariadení žiadame dodržať ochranné pásmo v zmysle zákona
251/2012 Z.z., a bezpečné vzdialenosti podľa príslušných noriem STN. Pri
realizácii výkopových prác, žiadame neporušiť celistvosť uzemňovacej sústavy.

2. Zároveň si Vás dovoľujeme upozorniť, že v danej lokalite sa môžu nachádzať aj
podzemné vedenia tretích osôb. V zmysle stavebného zákona je pre určenie
presnej trasy podzemných vedení potrebné fyzicky ju vytýčiť.

3. Presnú trasu podzemných káblových vedení v majetku SSD, a.s. Vám na základe
objednávky smerovanej na e-mailovú adresu prevadzkovateľúgssd.sk vytýči
určený pracovník SSD, a.s.

4. Pripojenie na elektrinu žiadame riešiť z distribučnej sústavy SSD v danej lokalite.
Ako odovzdávacie miesto a bod napojenia určujeme spínací a istiaci prvok:
detailný popis bodu pripojenia (skriňa PRIS5 č.3 v majetku SSD)

5. Odsúhlasená prúdová hodnota hlavného ističa pred elektromerom
In—3x25A mpér.

6. Pripojenie do distribučnej sústavy SSD bude realizované v zmysle platných STN
a zákona 251/2012 Z.z.. Pripojenie elektrickej prípojky, Vám zabezpečí výlučne
SSD po splnení podmienok pripojenia definovaných v tomto vyjadrení,
pripojovacej zmluve a po zaplatení pripojovacieho poplatku na účet SSD.

7. Pre pokračovanie procesu pripojenia je bezpodmienečne potrebné uzatvoriť pripoj
ováciu zmluvu s Prevádzkovateľom distribučnej sústavy, ktorú posielame spolu s
týmto vyjadrením.

8. Prívod z istiacej skrine do rozvádzača merania spotreby elektriny bude
vyhotovený káblom minimálne AYKY-J4Bxlómm? alebo CYKY 4x10 mm“
mechanicky chránený v ochrannej trubke (chráničke) a ukončený na hlavnom
ističi pred meraním (plombovateľná časť). Meranie elektriny bude umiestnené v
elektromerovom rozvádzači (ďalej RE) umiestnenom na verejne prístupnom
mieste - na hranici pozemku. Vyhotovenie RE musí vyhovovať platnému súboru
noriem STN EN 61439-1 až 5, pre prípadné blokovanie elektrospotrebičov musia
byť splnené technické a obchodné podmienky príslušnej sadzby. Pred
elektromerovým rozvádzačom RE musí byť voľný rovný priestor aspoň 800mm.V
rozvádzači RE dochádza k rozdeleniu napájacej sústavy z TN-C na sústavu TN-S,
ktorú je potrebné riešiť mimo plombovanú časť rozvádzača RE. Práce na
elektrickom prívode ako aj montáž smie realizovať Vami vybratá odborne
spôsobilá osoba v zmysle Vyhlášky 508/2009 Z.z. Podrobnosti o podmienkach
merania nájdete na našej internetovej stránke www.ssd.sk .

9. Pre pokračovanie procesu pripojenia je potrebné zo strany odberateľa vybudovať
elektrický prívod od bodu pripojenia definovaného v bode 5. tohto vyjadrenia po
RE vrátane RE v súlade s "Všeobecnými podmienkami k vyhotoveniu stavby a k

pripojeniu odberných miest do distribučnej sústavy spoločnosti Stredoslovenská
distribučná, a.s. (ďalej SSD)", ktoré sú zverejnené na stránkach www.ssd.sk.
Elektrický prívod je elektroenergetické zariadenie, ktoré slúži na pripojenie
odberného elektrického zariadenia od odbočenia z distribučnej sústavy (od bodu
pripojenia - miesta odovzdania kvality elektriny, elektrickej prípojky) po RE
umiestnený spravidla na verejne prístupnom mieste, a ktoré si buduje stavebník na
vlastné náklady. Elektrický prívod je súčasťou odberného elektrického zariadenia
odberateľa. SSD nezodpovedá, ani sa nijakým spôsobom nevyjadruje k
projektovej dokumentácii, umiestneniu, vybudovaniu a údržbe NN elektrického
Č.s.: 6502/2020-153763/2020-SU-PA str. 8
prívodu. Odberateľ je povinný mať pred vybudovaním elektrického prívodu
súhlasy majiteľov dotknutých nehnuteľností, na ktorých je tento elektrický prívod
umiestnený. Všetky potrebné úpravy na vybudovanie elektrického prívodu
vrátane RE si realizuje žiadateľ na vlastné náklady.

10. Každé novo vybudované odberné elektrické zariadenie odberateľa musí mať pred
požiadavkou na montáž určeného meradla vyhotovenú Správu o odbornej
prehliadke a odbornej skúške elektrického zariadenia (revíznu správu od
elektrického prívodu z odovzdávacieho miesta po elektromerový rozvádzač) v
súlade so zákonom č. 124/2006 Z.z. a Vyhláškou č. 508/2009 Z.z. a Protokol o
kusovej skúške elektromerového rozvádzača, a tieto je odberateľ povinný
predložiť na požiadanie SSD v súlade s $ 39 ods. 9 Zákona o energetike.

11. Po úhrade a pripísaní pripojovacieho poplatku na účet SSD, splnení Technických
podmienok SSD, Obchodných podmienok pripojenia, vybudovaní a označení
elektrického prívodu spolu s RE a splnení podmienok uvedených v príslušnom
vyjadrení, bude možné pripojenie odberného elektrického zariadenia odberateľa
do distribučnej sústavy SSD od okamihu, kedy odberateľ zašle do SSD „Čestné
prehlásenie žiadateľa o pripravenosti na pripojenie odberného elektrického
zariadenia do distribučnej sústavy SSD“ (ďalej „Čestné prehlásenie“), ktoré je
zverejnené na stránkach www.ssd.sk , a ku ktorému bude zo strany SSD zaslané
písomné potvrdenie o jeho prijatí. Následne SSD v zmysle lehoty podľa
pripojovacej zmluvy čl. I. "Špecifikácia odberného miesta, termín realizácie
pripojenia - Termín vybudovania elektroenergetického zariadenia SSD" zrealizuje
vybudovanie elektrickej prípojky a zaistí pripojenie elektrického prívodu a
elektromerového rozvádzača k distribučnej sústave.

12. Po potvrdení prijatia vyššie uvedeného Čestného prehlásenia zo strany SSD
(potvrdenie splnenia technických podmienok pripojenia) a po vybudovaní
elektroenergetického zariadenia (prípojky) zo strany SSD v termíne podľa
Zmluvy o pripojení, môže požiadať odberateľ o uzatvorenie Zmluvy o dodávke
elektriny alebo Zmluvy o združenej dodávke elektriny u vybratého dodávateľa
elektriny, ktorý následne pošle do SSD žiadosť o montáž určeného meradla a
pripojenie k distribučnej sústave v súlade s platnou legislatívou, čo sa bude
považovať za splnenie obchodných podmienok pripojenia. Po montáži určeného
meradla je možné zahájiť samotnú dodávku elektriny.

13. SSD upozorňuje, že v prípade, ak bude chcieť žiadateľ pre dané odberné miesto
priznať distribučnú sadzbu pre elektrické kúrenie alebo bude prostredníctvom
daného odberného miesta plánovaná realizácia napájania pre nabíjacie stanice
elektromobilov, je žiadateľ povinný požiadať o stanovisko SSD k takémuto
charakteru využívania odberného miesta.

Priznanie distribučných sadzieb viažúcich sa na spomínané charaktery odberu sú
podmienené predošlým vyjadrením SSD, v ktorom budú definované podmienky
pre priznanie týchto sadzieb.

14. SSD si Vás dovoľuje upozorniť, že v prípade úprav v elektrických rozvodoch na
odberných miestach v bytových domoch (napr. zmena hlavného ističa a pod.), je z
hľadiska bezpečnosti, potrebné pred každou zmenou informovať správcu
bytového domu o rozsahu danej zmeny. Následne by mal správca bytového domu
posúdiť vplyv navrhovaných zmien na existujúce inštalačné rozvody bytového
domu (stupačkové vedenia), ktoré sú v jeho správe tak, aby nedošlo k ohrozeniu
života, zdravia a majetku osôb.

15. Platnosť tohto vyjadrenia je 6 kalendárnych mesiacov od jeho vystavenia.
Vyjadrenie stráca platnosť pri zmene údajov, na základe ktorých bolo vydané
Č.s.: 6502/2020-153763/2020-SU-PA str. 9
(zmena lokality, vstupných údajov, súvisiacej legislatívy a pod.).

- plynová prípojka: v rámci výstavby rozvodu STL plynovodu v miestnej komunikácii
bol zrealizovaný pripojovací plynovod (verejná časť prípojky) - ukončený v skrinke
HUP. Domový plynovod je navrhnutý samostatný pre jednotlivé obytné jednotky.
Každá obytná jednotka bude mať samostatný plynový kotol.

V zmysle vyjadrenia SPP - Distribúcia a.s., č. TD/789/ZA/K1/2018 zo dňa

23.10.2018 - k dokumentácii pre stavebné konanie, k výstavbe plynárenského

zariadenia a k umiestneniu stavby z hľadiska bezpečnostných a ochranných pásiem

existujúcich plynárenských zariadení. Základné technické parametre navrhovaného

plynárenského zariadenia: existujúci pripojovací plynovod: D32 PE, dížka: 8,18 m

prevádzkový tlak: 100 kPa.

SPP-D súhlasí s realizáciou vyššie uvedenej stavby za dodržania nasledujúcich

podmienok:

VŠEOBECNÉ PODMIENKY:

- Stavebník je povinný dodržať ochranné a bezpečnostné pásma existujúcich
plynárenských zariadení v zmysle $79 a 880 Zákona o energetike,

- stavebník je povinný pri realizácii stavby dodržať minimálne vzájomné
vzdialenosti medzi navrhovanými plynárenskými zariadeniami a existujúcimi
nadzemnými a podzemnými objektmi a inžinierskymi sieťami v zmysle STN 73
6005, STN 73 3050 a TPP 906 01,

- pred realizáciou zemných prác a/alebo pred začatím vykonávania iných činností je
stavebník povinný zabezpečiť prostredníctvom príslušných prevádzkovateľov
presné vytýčenie všetkých existujúcich podzemných vedení,

- pred realizáciou zemných prác a/alebo pred začatím vykonávania iných činností,
je stavebník povinný požiadať SPP-D o presné vytýčenie existujúcich
plynárenských zariadení na základe písomnej objednávky, ktorú je potrebné
zaslať na adresu: SPP - distribúcia, a.s., Sekcia údržby, Mlynské Nivy 44/b, 825
11 Bratislava, alebo elektronicky, prostredníctvom online formuláru zverejneného
na webovom sídle SPP-D (www.spp-distribucia.sk),

- v záujme predchádzaniu poškodenia plynárenského zariadenia, ohrozenia jeho
prevádzky a/alebo prevádzky distribučnej siete, SPP-D vykonáva bezplatne
vytyčovanie plynárenských zariadení do vzdialenosti 100 m,

- stavebník je povinný pri realizácií stavby dodržiavať ustanovenia Zákona o
energetike, Vyhlášky č. 508/2009 Z.z., Stavebného zákona a iných všeobecne
záväzných právnych predpisov - súvisiacich technických noriem a Technických
pravidiel pre plyn (TPP), najmä TPP 702 012, TPP 609 01,

- stavebník je povinný pri realizácii dodržať technické podmienky stanovené v
predchádzajúcom vyjadrení SPP-D k Žiadosti o pripojenie k distribučnej sieti
číslo 7009291018.

TECHNICKÉ PODMIENKY:

- Stavebník je povinný zabezpečiť prístupnosť plynárenských zariadení počas
realizácie stavby z dôvodu potreby prevádzkovania plynárenských zariadení,
najmä výkonu kontroly prevádzky, údržby a výkonu odborných prehliadok a
odborných skúšok opráv, rekonštrukcie (obnovy) plynárenských zariadení,

- stavebník je povinný umožniť zástupcovi SPP-D vstup na stavenisko a výkon
kontroly realizácie činností v ochrannom pásme plynárenských zariadení,

- stavebník je povinný realizovať výkopové práce vo vzdialenosti menšej ako 1,50
m na každú strany od obrysu existujúcich plynárenských zariadení v súlade s STN
Č.s.: 6502/2020-153763/2020-SU-PA str. 10
73 3050 až po predchádzajúcom vytýčení plynárenských zariadení výhradne
ručne bez použitia strojových mechanizmov,

- prístup k akýmkoľvek technologickým zariadeniam SPP-D nie je povolený a
manipulácia s nimi je prísne zakázaná, pokiaľ sa na tieto práce nevzťahuje vydané
povolenie SPP-D,

- odkryté plynovody, káble, ostatné inžinierske siete musia byť počas odkrytia
zabezpečené proti poškodeniu,

- stavebník nesmie nad trasou plynovodu realizovať také terénne úpravy, ktoré by
zmenili jeho doterajšie krytie a híbku uloženia, v prípade zmeny úrovne terénu

požadujeme všetky zariadenia a poklopy plynárenských zariadení osadiť do novej
úrovne terénu,

- každé poškodenie zariadenia SPP-D, vrátane poškodenia izolácie potrubia, musí
byť ihneď ohlásené SPP-D na tel. č. : 0850 111 727,

- stavebník je povinný zabezpečiť, aby bez súhlasu SPP-D nedošlo k zmene polohy
hlavného uzáveru plynu (HUP),

- stavebník je povinný umiestniť hlavný uzáver plynu (HUP), regulátor tlaku plynu
( RTP ) a meradlo do skrinky DRZ na hranicu verejne prístupného a súkromného
pozemku tak, aby boli prístupné z verejného priestranstva.

OSOBITNÉ PODMIENKY:

- žiadne.

- napojenie na pozemné komunikácie: napojenie 2 novými vjazdami na prístupovú
účelovú komunikáciu ul. Tyrkysová (v súkromnom vlastníctve) - so súhlasom jej
vlastníka Budatin s.r.o. zo dňa 15.02.2018.

c) odvádzanie povrchových vôd: dažďová voda zo strechy objektu a odvodnenie
oporných múrov bude zvedené do navrhovanej spoločnej vsakovacej šachty na
pozemku investora.

d) úpravy okolia stavby a podmienky ochrany zelene : v zmysle vyjadrenia Okresného
úradu Žiline, OSŽP - štátna správa ochrany prírody a krajiny č. OU-ZA-OSZP3-
2019/044131-002/Bal zo dňa 31.10.2019,

e) vymedzenie rozsahu plôch pozemkov tvoriacich súčasť staveniska : pozemky C-KN
parc. č. 712/423, 712/424 v k. ú. Budatín,

f) podrobnosti pre opatrenia na susednom pozemku alebo na stavbe podľa $ 135
stavebného zákona : nevyžadujú sa,

g) meno (názov) a adresu (sídlo) zhotoviteľa stavby oznámiť po výberovom konaní
do 15 dní po skončení výberového konania,

h) pred začatím stavby, zmeny stavby, terénnych úprav a ťažobných prác na povrchu
musí stavebník zabezpečiť vytýčenie stavieb fyzickou osobou alebo právnickou
osobou oprávnenou vykonávať geodetické a kartografické činnosti a autorizačné
overenie vybraných geodetických a kartografických činností autorizovaným geodetom
a kartografom, v zmysle $ 75 ods.1 stavebného zákona,

1) stavenisko :

- zabezpečiť pred vstupom cudzích osôb na miesta, kde môže dôjsť k ohrozeniu
života alebo zdravia, a to prípadne aj úplným ohradením,

- označiť ako stavenisko s uvedením potrebných údajov o stavbe a účastníkoch
výstavby,

- musí mať zriadený vjazd a výjazd z miestnej komunikácie alebo z účelovej
komunikácie na prísun stavebných výrobkov, na odvoz zeminy a stavebného
Č.s.: 6502/2020-153763/2020-SU-PA str. 11
odpadu a na prístup vozidiel zdravotníckej pomoci a požiarnej ochrany, ktorý sa
musí čistiť,

- musí umožňovať bezpečné uloženie stavebných výrobkov a stavebných
mechanizmov a umiestnenie zariadenia staveniska,

- musí umožňovať bezpečný pohyb osôb vykonávajúcich stavebné práce,

- musí mať zabezpečený odvoz alebo likvidáciu odpadu,

- musí mať vybavenie potrebné na vykonávanie stavebných prác a na pobyt osôb
vykonávajúcich stavebné práce,

- musí byť zriadené a prevádzkované tak, aby bola zabezpečená ochrana zdravia
ľudí na stavenisku a v jeho okolí, ako aj ochrana životného prostredia podľa
osobitných predpisov,

- na stavenisku musí byť po celý čas výstavby projektová dokumentácia stavby
overená stavebným úradom, potrebná na uskutočňovanie stavby a na výkon
štátneho stavebného dohľadu.

4. Za realizovateľnosť projektu zodpovedá projektant, v zmysle $ 46 stavebného zákona.

5. Vzmysle $ 48 ods.l stavebného zákona sa stavba musí uskutočňovať v súlade
s overeným projektom a stavebným povolením. Podstatné zmeny nesmú byť vykonané
bez predchádzajúceho povolenia stavebného úradu.

6. Odprvého dňa prípravných prác až do skončenia stavebných prác viesť stavebný denník,
ako súčasť dokumentácie uloženej na stavenisku, v zmysle $ 46d, ods.1 a2 stavebného
zákona.

7. Neprekročiť prípustné hodnoty určené Vyhláškou č. 549/2007 Z.z. MZ SR, ktorou sa
ustanovujú podrobnosti o prípustných hodnotách hluku, infrazvuku a vibrácií a o
požiadavkách na objektivizáciu hluku, infrazvuku a vibrácií v životnom prostredí.

8. Pred zahájením výstavby je stavebník povinný odsúhlasiť všetky podzemné a nadzemné
vedenia s ich správcami a vytýčiť ich na tvare miesta, a pri realizácii stavebných objektov
je nutné dodržať príslušné normy STN alebo EN o križovaní a priestorovej úprave vedení
technického vybavenia územia, ako aj podmienky stanovené správcami inžinierskych
sietí.

9. Za prípadné škody spôsobené stavebnou činnosťou na susedných nehnuteľnostiach je
zodpovedný stavebník. Zodpovednosť za škodu reguluje šiesta časť, druhá hlava, zákona
č. 40/1964 Zb. Občianskeho zákonníka, v znení neskorších predpisov.

10. Dažďovú vodu zo strešnej konštrukcie stavby a odvodnenie oporných múrov zviesť do
navrhovanej spoločnej vsakovacej šachty na pozemku investora.

11. Toto rozhodnutie nenahrádza rozkopové povolenie, ktoré v prípade potreby pre zásah do
miestnej komunikácie vydá Mesto Žilina, Stavebný úrad, referát špeciálneho stavebného
úradu - doprava.

12. Vyprodukovaný odpad je stavebník povinný odovzdať oprávnenému subjektu na
zhodnotenie, alebo zneškodniť na povolenej skládke alebo vo vhodnom zariadení, v
zmysle zákona č. 79/2015 Z.z. oodpadoch ao zmene a doplnení niektorých zákonov,
v znení neskorších predpisov.

13. V zmysle $ 76 ods.1 stavebného zákona dokončenú stavbu, prípadne jej časť spôsobilú na
samostatné užívanie alebo tú časť stavby, na ktorej sa vykonala zmena alebo udržiavacie
práce, pokiaľ tieto stavby vyžadovali stavebné povolenie, možno užívať len na základe
kolaudačného rozhodnutia.
Č.s.: 6502/2020-153763/2020-SU-PA str. 12

14. Vzmysle $ 100 písm. a) stavebného zákona stavebník, oprávnená fyzická osoba či
právnická osoba uskutočňujúca stavbu, ako aj vlastník stavby, sú povinní umožniť
orgánom štátneho stavebného dohľadu animi prizvaných znalcom vstupovať na
stavenisko a do stavby, nazerať do jej dokumentácie a utvárať predpoklady pre výkon
dohľadu.

15. Celkový rozpočtový náklad stavby je : 160.000,- €.

16. Vzmysle $ 67 ods.2 stavebného zákona stavebné povolenie stráca platnosť, ak sa so
stavbou nezačalo do dvoch rokov odo dňa keď nadobudlo právoplatnosť, pokiaľ stavebný
úrad v odôvodnených prípadoch neurčil na začatie stavby dlhšiu lehotu.

17. Námietky účastníkov konania : neboli vznesené.

Odôvodnenie:

Dňa 12.03.2019 podal stavebník žiadosť o vydanie stavebného povolenia na uvedenú stavbu,

týmto dňom bolo začaté stavebné konanie. Uzemné rozhodnutie o umiestnení stavby bolo

vydané dňa 22.05.2018 pod č.sp. 2863/2018-30263/2018-OSP-PA, právoplatnosť nadobudlo

dňa 07.07.2018.

Pretože žiadosť nebola úplná a neboli doložené všetky podklady potrebné pre riadne

posúdenie prípustnosti stavby, stavebník bol dňa 25.09.2019 rozhodnutím č. 15257/2019-
43085/2019-OSP-PA vyzvaný na doplnenie žiadosti. Zároveň bolo stavebné konanie z tohto
dôvodu prerušené. Žiadosť bola doplnená dňa 06.11.2019.

Stavebný úrad dňa 27.03.2020 listom č. 6502/2020-29159/2020-SU-PA oznámil začatie
stavebného konania všetkým známym účastníkom konania a dotknutým orgánom. Stavebný
úrad podľa ustanovení $ 61 ods. 2 stavebného zákona upustil od ústneho pojednávania a
miestneho zisťovania, nakoľko pomery staveniska mu boli dobre známe a Žiadosť
poskytovala dostatočný podklad pre riadne a spoľahlivé posúdenie navrhovanej stavby.
Zároveň stanovil lehotu 7 dní odo dňa doručenia tohto oznámenia, v ktorej môžu účastníci
konania uplatniť svoje námietky a pripomienky a dotknuté orgány svoje stanoviská.
Dokumentácia stavby bola k dispozícii na stavebnom úrade. Uvedeným spôsobom stavebný
úrad umožnil účastníkom konania vyjadriť sa k podkladom stavebného povolenia.

Stavebný úrad v uskutočnenom správnom konaní preskúmal predloženú žiadosť z hľadísk
uvedených v $ 62 stavebného zákona, prerokoval ju s účastníkmi konania a s dotknutými
orgánmi a zistil, že jej realizáciou alebo užívaním nie sú ohrozené záujmy chránené
stavebným zákonom, predpismi vydanými na jeho uskutočnenie a osobitnými predpismi.

Pre vyššie uvedenú stavbu (stavebné objekty: SO 01 - Rodinné domy, SO 02 - Komunikácie
a spevnené plochy, SO 03 - Vodovod, SO 03.1 - Prípojky vodovodu, SO 04 - Kanalizácia
splašková, SO 04.1 - Prípojky splaškovej kanalizácie, SO 05 - Kanalizácia dažďová, SO 06 -
NN rozvody, SO 07 - Verejné osvetlenie, SO 08 - STL Plynovod, SO 08.1 - Prípojky
plynovodu, SO 09 - Rozvody MTS - slaboprúd, SO 09.1 - Prípojky MTS, SO 10 - Prekládka
vodovodných potrubí) Okresný úrad Žilina, OSŽP3 - na úseku EIA, pod č. OU-ZA-OSZP3-
2016/019919-003/Hnl vydal Rozhodnutie vydané v zisťovacom konaní dňa 15.08.2016,
ktoré nadobudlo právoplatnosť dňa 04.10.2016.

Dokument je prístupný na stránke: https://www.enviroportal.sk/sk/eia/detail/os-ibv-35-rd-
zilina-budatin-pod-komunikaciou.

Stavebný úrad podľa $ 140c ods.2 stavebného zákona požiadal dňa 27.03.2020 listom. č.
6502/2020-29168/2020-SU-PA Okresný úrad Žilina, OSŽP - na úseku EIA ovydanie
Č.s.: 6502/2020-153763/2020-SU-PA str. 13
záväzného stanoviska pre stavebné objekty, ktoré sú predmetom stavebného povolenia.
Záväzné súhlasné stanovisko Okresného úradu Žilina, OSŽP - na úseku EIA č. OU-ZA-
OSZP3-2020/021504-002/Hnl bolo vydané dňa 29.05.2020.

Stanoviská oznámili:

- Okresný úrad Žilina, OSŽP - oddelenie ochrany prírody a vybraných zložiek ŽP na úseku
EIA, Okresný úrad Zilina, OSZP - štátna správa ochrany prírody a krajiny, Slovak
Telekom a.s., Krajský pamiatkový úrad Žilina, OR HaZZ v Ziline, Mesto Zilina - odbor
ZP, SEVAK a.s.: SSD a.s.: SPP - Distribúcia a.s..

Stavebný úrad zaistil vzájomný súlad predložených stanovísk dotknutých orgánov

požadovaných osobitnými predpismi, zabezpečil plnenie požiadaviek vlastníkov a správcov

pre napojenie do sietí technického vybavenia aich stanoviská a požiadavky zahrnul do
podmienok tohto rozhodnutia.

Projektová dokumentácia stavby splňa všeobecné technické požiadavky na výstavbu a

podmienky územného rozhodnutia o umiestnení predmetnej stavby. Stavebný úrad v priebehu

stavebného konania nezistil dôvody, ktoré by bránili vydaniu stavebného povolenia stavby.

Stavebný úrad preto rozhodol tak, ako je uvedené vo výroku tohto rozhodnutia.

Účastníci konania:

- Miroslav Takáč, Budatin, s.r.o., Ing. Tomáš Borovský, Lucia Borovská, Ing. Dušan
Hanulják, Be. Zuzana Rohoňová, Gynekologická ambulancia - MUDr. Richard Láni,
s.r.o., Združenie domových samospráv, Martin Dúbravka, Lucia Peštová, Ateliér FS s.r.o.
- Ing. Václav First.

Námietky účastníkov neboli v konaní uplatnené.

Upozornenie:

Stavebné povolenie stráca podľa $67 stavebného zákona platnosť, ak sa s uskutočnením

stavby nezačne do dvoch rokov odo dňa nadobudnutia právoplatnosti tohto rozhodnutia.

So stavbou nesmie byť začaté, pokiaľ stavebné povolenie nenadobudne právoplatnosť ($ 52

zákona č. 71/1967 Zb. o správnom konaní (správny poriadok) v znení neskorších predpisov).

V zmysle $ 69 ods. 3 stavebného zákona, stavebné povolenie alebo rozhodnutie, ktorým sa

zamietla žiadosť o udelenie stavebného povolenia týkajúceho sa stavby, vo vzťahu ku ktorej

sa uskutočnilo posudzovanie vplyvov alebo zisťovacie konanie podľa osobitného predpisu
1ga) zverejní stavebný úrad bezodkladne na svojej úradnej tabuli a na svojom webovom sídle.

Ak zverejnenie na webovom sídle nie je možné, zverejní ho stavebný úrad len na úradnej

tabuli. Rozhodnutie musí byť zverejnené odo dňa jeho vydania až do nadobudnutia jeho

právoplatnosti. :

Poučenie o odvolaní:

Proti tomuto rozhodnutiu podľa $ 53 a $ 54 zákona č. 71/1967 Zb. o správnom konaní

(správny poriadok) v znení neskorších právnych predpisov možno podať odvolanie do 15

dní odo dňa oznámenia rozhodnutia na správnom orgáne, ktorý napadnuté rozhodnutie vydal.

Včas podané odvolanie má odkladný účinok. Rozhodnutie, ktoré po vyčerpaní riadnych

opravných prostriedkov nadobudlo právoplatnosť, možno preskúmať súdom podľa zákona č.

162/2015 Z. z. správny súdny poriadok v znení neskorších platných právnych predpisov.
Č.s.: 6502/2020-153763/2020-SU-PA str. 14
Proti tomuto rozhodnutiu podľa $ 140c ods. 8 stavebného zákona má právo podať odvolanie
aj ten, kto nebol účastníkom konania, ale len v rozsahu vakom namieta nesúlad povolenia
s obsahom rozhodnutia podľa osobitného predpisu ($ 29 ods. 12, $ 37 ods. | a $ 19 0ds. 1
zákona č. 24/2006 Z.z. v znení neskorších predpisov) a to na tunajší stavebný úrad.
Lehota na podanie odvolania podľa $ 140c ods. 9 stavebného zákona je 15 pracovných dní
a začína plynúť odo dňa zverejnenia tohto rozhodnutia na úradnej tabuli a internetovej stránke
správneho orgánu - mesta Zilina.
Podaním odvolania podľa $ 140c ods. 10 stavebného zákona sa ten, kto ho podal, stáva
účastníkom konania.
UA? popr š
77 <
© r je —
a (50)
M ä Mgr. Peter Fiabáne
rimátor
a B- A g
no ža
Toto rozhodnutie sa zverejňuje na úradnej tabuli mesta Žilina a internetovej stránke
mesta www.zilina.sk v zmysle $ 69 ods. 3 stavebného zákona. Zverejnené bude odo dňa
jeho vydania až do nadobudnutia jeho právoplatnosti.
K Pa —V——65G—VG6—V—ÔÔúÔOävú
Pečiatka a podpis orgánu, ktorý potvrdzuje vyvesenie a zvesenie oznámenia na úradnej tabuli
a internetovej stránke.
ZVEREJNENÉ NA URADNEJ TABULI
MESTA Ži LINA A INTERNETOVEJ
STRÁNKE MESTA ŽILINA
Vyvesené od: | A. BD
Vyvesené do: ....issnesonnsncnvcnonns non
Alt ZNESOÍAŤ vy Ni
Počet zverejnených strán: a SDÍŤ nn
Podpis zodpovednej osoby: .......esornernrrnoonecnom
ai:
Poplatok:
Správny poplatok podľa zákona č. 145/1995 Z.z. o správnych poplatkoch v znení neskorších
predpisov položky 60 písm. a ods. 1 vo výške 50.00 €, položky 60 písm. d ods. 5 vo výške
30.00 €, celkom 80.00 € bol zaplatený dňa 12.03.2019.
Príloha:
- overená situácia
- overená projektová dokumentácia pre stavebníka
Doručí sa:
Účastníci
1. Miroslav Takáč, Radlinského 296/3, 01001 Zilina
2. Budatin, s.r.o., Murgašova 2/243, 010 01 Zilina
Č.s.: 6502/2020-153763/2020-SU-PA str. 15
3. Ing. Tomáš Borovský, Jána Kalinčiaka súp. č. 92/18, 010 01 Žilina

4. Lucia Borovská, Jána Kalinčiaka 92/18, 010 01 Žilina

5. Ing. Dušan Hanulják, Záhradná 650, 013 24 Strečno

6. Bc. Zuzana Rohoňová, Turčiansky Peter 53, 038 41 Košťany nad Turcom

7. Gynekologická ambulancia - MUDr. Richard Láni, s.r.o., Petzvalova súp. č. 3371/29, 010
15 Žilina

8. Združenie domových samospráv, Námestie SNP č.13, P.O. BOX 218, 850 00 Bratislava
9. Martin Dúbravka, Na Sihoti súp. č. 555/5, Staré mesto, 010 01 Žilina

10. Lucia Peštová, Hečkova súp. č. 2520/27, Bôrik, 010 01 Žilina

Projektant

11. Ateliér FS, s.r.o., Ing. Václav First, Hviezdoslavova 106, 026 01 Vyšný Kubín

Na vedomie

12. KPÚ Žilina, Mariánske námestie č.19, 010 01 Žilina

13. OÚ Žilina, Odbor lesný a pozemkový, Vysokoškolákov 8556/33B, 010 08 Žilina

14. OÚ Žilina, Odbor starostlivosti o ŽP, na úseku EIA, Vysokoškolákov 8556/33B, 010 08
Žilina

15. Slovak Telekom a.s., Bajkalská 28, 817 62 Bratislava

16. SPP-distribúcia, a.s. Mlynské nivy 44/b, 825 11 Bratislava 26

17. Stredoslovenská distribučná a.s., Pri Rajčianke súp. č. 2927/8, 010 47 Žilina

18. SEVAK a.s., Bôrická cesta súp. č. 1960, 010 57 Žilina

19. UPC Broadband Slovakia s.r.o., Ševčenkova 36, 851 01 Bratislava

20. OR HaZZ, Nám. požiarnikov 1, 010 01 Žilina

21. OÚ Žilina, Odbor starostlivosti o ŽP - št. správa ochrany prírody a krajiny,
Vysokoškolákov 8556/33B, 010 08 Žilina

22. Mesto Žilina, Odbor správy verejného priestranstva a životného prostredia

23. Mesto Žilina, Stavebný úrad, referát špeciálneho stavebného úradu - doprava

24. Mesto Žilina, primátor

';

        $resolver = new CadasterNumber();
        $number = $resolver->resolve($text);

        $this->assertEquals(12345, $number);
        $this->assertEquals([
            '712/423',
        ], array_values($resolver->parcels));

    }
}
