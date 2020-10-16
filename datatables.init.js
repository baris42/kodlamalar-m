/**
 * Theme: Ubold Admin Template
 * Author: Coderthemes
 * Component: Datatable
 *
 */
var handleDataTableButtons = function() {
        "use strict";
        0 !== $("#datatable-buttons").length && $("#datatable-buttons").DataTable({
            dom: "Bfrtip",
            buttons: [{
                extend: "copy",
                className: "btn-sm",
                footer: true,
                title:'Ürün Raporlama',
                orientation: 'landscape',
                pageSize: 'LEGAL',
                exportOptions: {
                    columns: [0,1,2,3,4,5,6,7,8,9]
                },
            }, {
                extend: "csv",
                className: "btn-sm",
                footer: true,
                title:'Ürün Raporlama',
                orientation: 'landscape',
                pageSize: 'LEGAL',
                exportOptions: {
                    columns: [0,1,2,3,4,5,6,7,8,9]
                },
            }, {
                extend: "excel",
                className: "btn-sm",
                footer: true,
                title:'Ürün Raporlama',
                orientation: 'landscape',
                pageSize: 'LEGAL',
                exportOptions: {
                    columns: [0,1,2,3,4,5,6,7,8,9]
                },
            }, {
                extend: "pdf",
                download: "open",
                className: "btn-sm",
                footer: true,
                title:'Ürün Raporlama',
                orientation: 'landscape',
                pageSize: 'LEGAL',
                exportOptions: {
                    columns: [0,1,2,3,4,5,6,7,8,9]
                },
            }, {
                extend: "print",
                className: "btn-sm",
                footer: true,
                title:'Ürün Raporlama',
                orientation: 'landscape',
                pageSize: 'LEGAL',
                exportOptions: {
                    columns: [0,1,2,3,4,5,6,7,8,9]
                },
            }
            ],"columnDefs": [
                {
                    "targets": [9],
                    "visible": false,
                    "searchable": false
                }

            ],
            responsive: !0,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Turkish.json"
            }
        })
    },
    TableManageButtons = function() {
        "use strict";
        return {
            init: function() {
                handleDataTableButtons()
            }
        }
    }();