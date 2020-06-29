

var db = new Dexie("rpi-themenseiten");

db.version(3).stores({
    themenseiten: 'id, titel, url',
    themengruppen: 'id,themenid, titel',
    materialien: '++id, material, gruppenid, titel, url'
});
db.open();
const all =  db.themenseiten.toArray().then( function (response) {
});

var themenseiten = new Object();
var themengruppen = new Object();
var materialien = new Object();

db.themenseiten.toArray().then( function (response) {
    themenseiten = response;
});
db.themengruppen.toArray().then( function (response) {
    themengruppen = response;
});
db.materialien.toArray().then( function (response) {
    materialien = response;
});

function FillThemenseitenDB ( id ) {
    // DB verwerfen und neu aufbauen
    db.delete().then(() => {
        console.log("Database successfully deleted");
    }).catch((err) => {
        console.error("Could not delete database");
    }).finally(() => {
        db.version(3).stores({
            themenseiten: 'id, titel, url',
            themengruppen: 'id,themenid, titel',
            materialien: '++id, materialid, gruppenid, titel, url'
        });
        db.open();
        const element = document.getElementById( "themenedit"+id);
        var data =  decodeURIComponent( element.getAttribute('data-themenedit').replace(/\+/g, '%20') );
        var obj = JSON.parse( data );

        db.themenseiten.add({id: obj.themenseiten[0].id, titel: obj.themenseiten[0].titel, url: obj.themenseiten[0].url});
        for (var i in obj.themengruppen ) {
            db.themengruppen.add({id: obj.themengruppen[i].id, themenid: obj.themengruppen[i].themenid, titel: obj.themengruppen[i].titel});
        }
        for (var i in obj.materialien ) {
            db.materialien.add({materialid: obj.materialien[i].id, gruppenid: obj.materialien[i].gruppenid, titel: obj.materialien[i].titel, url: obj.materialien[i].url});
        }
    });
};
jQuery(document).ready(function(){

    if (typeof  jQuery.contextMenu != 'undefined' && Object.keys(themenseiten).length !== 0) {
        jQuery.contextMenu({
            selector: '.themenseitenedit',
            trigger: 'left',
            build: function ($trigger, e) {
                var items = new Object();
                items['titel_' + themenseiten[0].id ] = {name: "Themenseite: " + themenseiten[0].titel}
                items['sep1'] = "---------";
                for (var tg in themengruppen) {
                    var material = new Object;
                    material['add_' + themengruppen[tg].id ] = {name: "Material hinzufügen" };
                    material['sep' + tg] = "---------";
                    for (var m in materialien) {
                        if (themengruppen[tg].id == materialien[m].gruppenid) {
                            material["m_" + materialien[m].materialid] = {name: materialien[m].titel};
                        }
                    }
                    items["tg_" + tg + "_"  + themengruppen[tg].themenid] = {name: themengruppen[tg].titel, items: material};
                }
                items['sep2'] = "---------";
                items['quit'] = {
                    name: "Speichern und Beenden",
                };
                return {
                    callback: function (key, opt, $trigger) {
                        res = key.split("_");
                        if ( res[0] == "m") {
                            // Vorhandenes Material angeklickt, URL holen und dorthin weiterleiten.
                            db.materialien.where("id").equals( parseInt( res[1] ) ).toArray().then( function (response) {
                                var win = window.open(response[0].url, '_blank');
                                win.focus();
                            });
                        }
                        if ( res[0] == "titel") {
                            // Vorhandene Themengruppe angeklickt, Themenseiten URL holen und dorthin weiterleiten.
                            db.themenseiten.where("id").equals( parseInt( res[1] ) ).toArray().then( function (response) {
                                var win = window.open(response[0].url, '_blank');
                                win.focus();
                            });
                        }
                        if ( res[0] == "add") {
                            // Material einer Themengruppe hinzufügen
                            var themengruppe = res[1];
                            var url = opt.$trigger[0].getAttribute('data-materialurl');
                            var id = opt.$trigger[0].getAttribute('data-materialid');
                            var titel = opt.$trigger[0].getAttribute('data-materialtitel');
                            db.materialien.add({materialid: parseInt(id), gruppenid: themengruppe, titel: titel, url: url });
                            db.materialien.toArray().then( function (response) {
                                materialien = response;
                            });
                        }
                        if ( res[0] == "quit") {
                            // Daten an Materialpool übergeben und DB löschen.
                            var data = {
                                'action': 'mp_update_themenseite',
                                'material': materialien,
                                'themenseite' : themenseiten,
                            };
                            jQuery.post(ajaxurl, data, function(response ) {
                                ret = response;
                                db.delete();
                                themenseiten = new Object();
                            });
                        }
                    },
                    items: items
                };
            }
        });
    };
});
