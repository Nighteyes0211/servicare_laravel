<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'auftraggeber', 'ansprechpartner', 'telefon', 'service_beleg_nr', 'ab_nr', 'rekla_sa_nr',
        'debit_nr', 'reklamation', 'reparatur', 'rep_aufnahme', 'wartung', 'schulung', 'auslieferung',
        'bfk', 'kb', 'pb', 'nt', 'km', 'sonstiges', 'typ', 'serien_nr', 'funktion_in_ordnung',
        'funktion_nicht_in_ordnung', 'materiallieferung', 'material_data', 'bemerkungen', 'datum',
        'anfahrtzeit', 'ruckfahrtzeit', 'fahrt_km', 'pausch_anfahrt', 'wartezeit', 'arbeitszeit',
        'ges_arbeitszeit', 'personenzahl', 'hotel_ubernachtung', 'hotel_von', 'hotel_bis', 'arbeit_fertig',
        'kostenpflichtig', 'unter_vorbehalt', 'sign_date', 'techniker_name', 'kunde_name', 'kunde_unterschrift',
        'auftr_nr', 'kostenst', 'aart_der_ausgefuhrten', 'rsl', 'iso', 'iea',
        'STK_bestanden_ja', 'funktiontest_besttanden_ja', 'funktiontest_besttanden_nein', 'kostenpflichtig_nein',
        'STK_bestanden_nein','arbeit_fertig_nein','notizen', 'servicebericht_both_beschreibung','datum_bis',

    ];
}
