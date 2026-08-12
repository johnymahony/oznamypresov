@extends('layouts.frontend')

@section('content_frontend')
    <style>
        p, ol li {
            font-size: 120%;
        }
    </style>
    <div class="page policy-page p-6">
        <h1>Ako to funguje?</h1>
        <p>
            Po zverejnení oznamu na elektronickej úradnej tabuli je daný oznam prenesený na túto stránku. Oznamy však
            samé o
            sebe neobsahujú žiadne údaje o geografickej polohe.
        </p>
        <p>
            Informáciu o konkrétnej oblasti, ktorej sa oznam týka, systém automaticky rozpoznáva z textu dokumentu
            (podľa
            názvov ulíc v text, resp. parcelných čísel)
        </p>
        <p>
            Niekedy sa to nepodarí. V takomto prípade sa geolokalizačné informácie pridávajú manuálne.
        </p>
        <h2>
            <a name="mapa"></a>
            Prečo nie sú všetky oznamy zaznačené na mape?
        </h2>
        <p>
            Lebo mnohé z nich sa týkajú celého mesta (napr informácie o voľbách, všeobecne záväzné nariadenia, atď...), alebo sa týkajú celých ulíc.
        </p>
        <h2>
            Prečo mi niektoré oznamy prídu až niekoľko dní neskôr?
        </h2>
        <p>
            Je to kvôli tomu, že oznamy, ktoré sa nespracujú automaticky, čakajú, že to niekto spracuje manuálne. A ten
            môže byť na
            dovolenke, odcestovaný, atď.
        </p>
        <h2>
            Chodí mi príliš veľa emailov
        </h2>
        <p>
            V takomto prípade skúste prehodnotiť odoberané kategórie, alebo zúžte oblasti záujmu.
        </p>
        <h2>
            Niekedy je to dosť nuda
        </h2>
        <p>
            Treba si uvedomiť, že úradníci nezverejňujú oznamy svojvoľne, ale zverejňujú to, čo im prikazuje zákon.
            Často
            krát sú to aj menej podstatné informácie (informácie o zrušení trvalého pobytu, informácie o uložení
            zásielky,
            atď.).
        </p>
        <p>
            Preto sme naprogramovali možnosť odoberať iba určité kategórie.
        </p>
        <h2>
            Politika ma nezaujíma, odhlásim sa?
        </h2>
        <p>
            Napriek tomu, že vás stavebné rozhodnutia, všeobecné záväzné nariadenia alebo oznámenia samosprávy
            nezaujímajú,
            neodhlasujte sa. Odporúčam vám odoberať oznamy o prerušení elektriny či výruby krovín. Tie sa vždy spájajú s
            konkrétnou oblasťou, a nie sú také časté. Nebudete ich dostávať veľa.
        </p>
        <h2>
            Kto to celé platí?
        </h2>
        <p>
            Náklady na prevádzku tohto portálu sú zhruba 20 € ročne. Zaplatil som to ja, lebo to považujem za dobrú vec.
            Veľká časť bola naprogramovaná zadarmo na Prvom šarišskom hackathone, ktorý zorganizoval mestský úrad. Na
            moje
            veľké potešenie sa mi ozvali niektorí občania, že sa im tento nápad veľmi páči a prispeli finančne. Za tie
            peniaze som kúpil reklamu na Facebooku.
        </p>
        <h2>
            Čo bude ďalej?
        </h2>
        <p>
            Priznám sa, veľmi rád by som doprogramoval ďalšie funkcie:
        </p>
        <ol>
            <li>Napríklad grafy. Zrozumiteľne zobrazené údaje o rozpočtoch, podnetoch od občanov, výdavkoch mesta, atď.
            </li>
            <li>Alebo prehľad o faktúrach a objednávkach ktoré mesto objednáva a platí. Áno, aj takéto informácie nám
                mesto
                ponúka. Vedel by som to zrátať a poslať Vám prehľad o tom, čo sa deje.
            </li>
            <li>Myslím si, že by bolo zaujímavé odoberať podnety od občanov. Napríklad, ak niekto vo Vašom okolí vytvorí
                na
                stránke mesta podnet, hneď by ste sa tom dozvedeli. Taktiež by ste dostali výsledok a riešenie -
                odpovede
                úradníkov na daný problém.
            </li>
        </ol>
        <p>
            Ale sám to už vo voľnom čase nezvládnem, preto hľadám niekoho, kto by mi s tým pomohol. Prácou -
            programovaním (PHP, MySql, JavaScript),
            alebo finančne.
        </p>
        <br>
        <h3>Ďakujeme za Vašu podporu.</h3>
        <h4>Ivan Ivanco</h4>
        <br>
        <br>
        @if(auth()->guest())
            @include('subscribe_popup')
        @endif
    </div>
@endsection