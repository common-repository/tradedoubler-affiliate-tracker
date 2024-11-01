//jQuery
const $ = jQuery;
// COUNTRIES FOR SELECT

const isoCountries = [
  {
    id: "AF",
    text: "Afghanistan",
  },
  {
    id: "AX",
    text: "Aland Islands",
  },
  {
    id: "AL",
    text: "Albania",
  },
  {
    id: "DZ",
    text: "Algeria",
  },
  {
    id: "AS",
    text: "American Samoa",
  },
  {
    id: "AD",
    text: "Andorra",
  },
  {
    id: "AO",
    text: "Angola",
  },
  {
    id: "AI",
    text: "Anguilla",
  },
  {
    id: "AQ",
    text: "Antarctica",
  },
  {
    id: "AG",
    text: "Antigua And Barbuda",
  },
  {
    id: "AR",
    text: "Argentina",
  },
  {
    id: "AM",
    text: "Armenia",
  },
  {
    id: "AW",
    text: "Aruba",
  },
  {
    id: "AU",
    text: "Australia",
  },
  {
    id: "AT",
    text: "Austria",
  },
  {
    id: "AZ",
    text: "Azerbaijan",
  },
  {
    id: "BS",
    text: "Bahamas",
  },
  {
    id: "BH",
    text: "Bahrain",
  },
  {
    id: "BD",
    text: "Bangladesh",
  },
  {
    id: "BB",
    text: "Barbados",
  },
  {
    id: "BY",
    text: "Belarus",
  },
  {
    id: "BE",
    text: "Belgium",
  },
  {
    id: "BZ",
    text: "Belize",
  },
  {
    id: "BJ",
    text: "Benin",
  },
  {
    id: "BM",
    text: "Bermuda",
  },
  {
    id: "BT",
    text: "Bhutan",
  },
  {
    id: "BO",
    text: "Bolivia",
  },
  {
    id: "BA",
    text: "Bosnia And Herzegovina",
  },
  {
    id: "BW",
    text: "Botswana",
  },
  {
    id: "BV",
    text: "Bouvet Island",
  },
  {
    id: "BR",
    text: "Brazil",
  },
  {
    id: "IO",
    text: "British Indian Ocean Territory",
  },
  {
    id: "BN",
    text: "Brunei Darussalam",
  },
  {
    id: "BG",
    text: "Bulgaria",
  },
  {
    id: "BF",
    text: "Burkina Faso",
  },
  {
    id: "BI",
    text: "Burundi",
  },
  {
    id: "KH",
    text: "Cambodia",
  },
  {
    id: "CM",
    text: "Cameroon",
  },
  {
    id: "CA",
    text: "Canada",
  },
  {
    id: "CV",
    text: "Cape Verde",
  },
  {
    id: "KY",
    text: "Cayman Islands",
  },
  {
    id: "CF",
    text: "Central African Republic",
  },
  {
    id: "TD",
    text: "Chad",
  },
  {
    id: "CL",
    text: "Chile",
  },
  {
    id: "CN",
    text: "China",
  },
  {
    id: "CX",
    text: "Christmas Island",
  },
  {
    id: "CC",
    text: "Cocos (Keeling) Islands",
  },
  {
    id: "CO",
    text: "Colombia",
  },
  {
    id: "KM",
    text: "Comoros",
  },
  {
    id: "CG",
    text: "Congo",
  },
  {
    id: "CD",
    text: "Congo}, Democratic Republic",
  },
  {
    id: "CK",
    text: "Cook Islands",
  },
  {
    id: "CR",
    text: "Costa Rica",
  },
  {
    id: "CI",
    text: "Cote D'Ivoire",
  },
  {
    id: "HR",
    text: "Croatia",
  },
  {
    id: "CU",
    text: "Cuba",
  },
  {
    id: "CY",
    text: "Cyprus",
  },
  {
    id: "CZ",
    text: "Czech Republic",
  },
  {
    id: "DK",
    text: "Denmark",
  },
  {
    id: "DJ",
    text: "Djibouti",
  },
  {
    id: "DM",
    text: "Dominica",
  },
  {
    id: "DO",
    text: "Dominican Republic",
  },
  {
    id: "EC",
    text: "Ecuador",
  },
  {
    id: "EG",
    text: "Egypt",
  },
  {
    id: "SV",
    text: "El Salvador",
  },
  {
    id: "GQ",
    text: "Equatorial Guinea",
  },
  {
    id: "ER",
    text: "Eritrea",
  },
  {
    id: "EE",
    text: "Estonia",
  },
  {
    id: "ET",
    text: "Ethiopia",
  },
  {
    id: "FK",
    text: "Falkland Islands (Malvinas)",
  },
  {
    id: "FO",
    text: "Faroe Islands",
  },
  {
    id: "FJ",
    text: "Fiji",
  },
  {
    id: "FI",
    text: "Finland",
  },
  {
    id: "FR",
    text: "France",
  },
  {
    id: "GF",
    text: "French Guiana",
  },
  {
    id: "PF",
    text: "French Polynesia",
  },
  {
    id: "TF",
    text: "French Southern Territories",
  },
  {
    id: "GA",
    text: "Gabon",
  },
  {
    id: "GM",
    text: "Gambia",
  },
  {
    id: "GE",
    text: "Georgia",
  },
  {
    id: "DE",
    text: "Germany",
  },
  {
    id: "GH",
    text: "Ghana",
  },
  {
    id: "GI",
    text: "Gibraltar",
  },
  {
    id: "GR",
    text: "Greece",
  },
  {
    id: "GL",
    text: "Greenland",
  },
  {
    id: "GD",
    text: "Grenada",
  },
  {
    id: "GP",
    text: "Guadeloupe",
  },
  {
    id: "GU",
    text: "Guam",
  },
  {
    id: "GT",
    text: "Guatemala",
  },
  {
    id: "GG",
    text: "Guernsey",
  },
  {
    id: "GN",
    text: "Guinea",
  },
  {
    id: "GW",
    text: "Guinea-Bissau",
  },
  {
    id: "GY",
    text: "Guyana",
  },
  {
    id: "HT",
    text: "Haiti",
  },
  {
    id: "HM",
    text: "Heard Island & Mcdonald Islands",
  },
  {
    id: "VA",
    text: "Holy See (Vatican City State)",
  },
  {
    id: "HN",
    text: "Honduras",
  },
  {
    id: "HK",
    text: "Hong Kong",
  },
  {
    id: "HU",
    text: "Hungary",
  },
  {
    id: "IS",
    text: "Iceland",
  },
  {
    id: "IN",
    text: "India",
  },
  {
    id: "ID",
    text: "Indonesia",
  },
  {
    id: "IR",
    text: "Iran}, Islamic Republic Of",
  },
  {
    id: "IQ",
    text: "Iraq",
  },
  {
    id: "IE",
    text: "Ireland",
  },
  {
    id: "IM",
    text: "Isle Of Man",
  },
  {
    id: "IL",
    text: "Israel",
  },
  {
    id: "IT",
    text: "Italy",
  },
  {
    id: "JM",
    text: "Jamaica",
  },
  {
    id: "JP",
    text: "Japan",
  },
  {
    id: "JE",
    text: "Jersey",
  },
  {
    id: "JO",
    text: "Jordan",
  },
  {
    id: "KZ",
    text: "Kazakhstan",
  },
  {
    id: "KE",
    text: "Kenya",
  },
  {
    id: "KI",
    text: "Kiribati",
  },
  {
    id: "KR",
    text: "Korea",
  },
  {
    id: "KW",
    text: "Kuwait",
  },
  {
    id: "KG",
    text: "Kyrgyzstan",
  },
  {
    id: "LA",
    text: "Lao People's Democratic Republic",
  },
  {
    id: "LV",
    text: "Latvia",
  },
  {
    id: "LB",
    text: "Lebanon",
  },
  {
    id: "LS",
    text: "Lesotho",
  },
  {
    id: "LR",
    text: "Liberia",
  },
  {
    id: "LY",
    text: "Libyan Arab Jamahiriya",
  },
  {
    id: "LI",
    text: "Liechtenstein",
  },
  {
    id: "LT",
    text: "Lithuania",
  },
  {
    id: "LU",
    text: "Luxembourg",
  },
  {
    id: "MO",
    text: "Macao",
  },
  {
    id: "MK",
    text: "Macedonia",
  },
  {
    id: "MG",
    text: "Madagascar",
  },
  {
    id: "MW",
    text: "Malawi",
  },
  {
    id: "MY",
    text: "Malaysia",
  },
  {
    id: "MV",
    text: "Maldives",
  },
  {
    id: "ML",
    text: "Mali",
  },
  {
    id: "MT",
    text: "Malta",
  },
  {
    id: "MH",
    text: "Marshall Islands",
  },
  {
    id: "MQ",
    text: "Martinique",
  },
  {
    id: "MR",
    text: "Mauritania",
  },
  {
    id: "MU",
    text: "Mauritius",
  },
  {
    id: "YT",
    text: "Mayotte",
  },
  {
    id: "MX",
    text: "Mexico",
  },
  {
    id: "FM",
    text: "Micronesia}, Federated States Of",
  },
  {
    id: "MD",
    text: "Moldova",
  },
  {
    id: "MC",
    text: "Monaco",
  },
  {
    id: "MN",
    text: "Mongolia",
  },
  {
    id: "ME",
    text: "Montenegro",
  },
  {
    id: "MS",
    text: "Montserrat",
  },
  {
    id: "MA",
    text: "Morocco",
  },
  {
    id: "MZ",
    text: "Mozambique",
  },
  {
    id: "MM",
    text: "Myanmar",
  },
  {
    id: "NA",
    text: "Namibia",
  },
  {
    id: "NR",
    text: "Nauru",
  },
  {
    id: "NP",
    text: "Nepal",
  },
  {
    id: "NL",
    text: "Netherlands",
  },
  {
    id: "AN",
    text: "Netherlands Antilles",
  },
  {
    id: "NC",
    text: "New Caledonia",
  },
  {
    id: "NZ",
    text: "New Zealand",
  },
  {
    id: "NI",
    text: "Nicaragua",
  },
  {
    id: "NE",
    text: "Niger",
  },
  {
    id: "NG",
    text: "Nigeria",
  },
  {
    id: "NU",
    text: "Niue",
  },
  {
    id: "NF",
    text: "Norfolk Island",
  },
  {
    id: "MP",
    text: "Northern Mariana Islands",
  },
  {
    id: "NO",
    text: "Norway",
  },
  {
    id: "OM",
    text: "Oman",
  },
  {
    id: "PK",
    text: "Pakistan",
  },
  {
    id: "PW",
    text: "Palau",
  },
  {
    id: "PS",
    text: "Palestinian Territory}, Occupied",
  },
  {
    id: "PA",
    text: "Panama",
  },
  {
    id: "PG",
    text: "Papua New Guinea",
  },
  {
    id: "PY",
    text: "Paraguay",
  },
  {
    id: "PE",
    text: "Peru",
  },
  {
    id: "PH",
    text: "Philippines",
  },
  {
    id: "PN",
    text: "Pitcairn",
  },
  {
    id: "PL",
    text: "Poland",
  },
  {
    id: "PT",
    text: "Portugal",
  },
  {
    id: "PR",
    text: "Puerto Rico",
  },
  {
    id: "QA",
    text: "Qatar",
  },
  {
    id: "RE",
    text: "Reunion",
  },
  {
    id: "RO",
    text: "Romania",
  },
  {
    id: "RU",
    text: "Russian Federation",
  },
  {
    id: "RW",
    text: "Rwanda",
  },
  {
    id: "BL",
    text: "Saint Barthelemy",
  },
  {
    id: "SH",
    text: "Saint Helena",
  },
  {
    id: "KN",
    text: "Saint Kitts And Nevis",
  },
  {
    id: "LC",
    text: "Saint Lucia",
  },
  {
    id: "MF",
    text: "Saint Martin",
  },
  {
    id: "PM",
    text: "Saint Pierre And Miquelon",
  },
  {
    id: "VC",
    text: "Saint Vincent And Grenadines",
  },
  {
    id: "WS",
    text: "Samoa",
  },
  {
    id: "SM",
    text: "San Marino",
  },
  {
    id: "ST",
    text: "Sao Tome And Principe",
  },
  {
    id: "SA",
    text: "Saudi Arabia",
  },
  {
    id: "SN",
    text: "Senegal",
  },
  {
    id: "RS",
    text: "Serbia",
  },
  {
    id: "SC",
    text: "Seychelles",
  },
  {
    id: "SL",
    text: "Sierra Leone",
  },
  {
    id: "SG",
    text: "Singapore",
  },
  {
    id: "SK",
    text: "Slovakia",
  },
  {
    id: "SI",
    text: "Slovenia",
  },
  {
    id: "SB",
    text: "Solomon Islands",
  },
  {
    id: "SO",
    text: "Somalia",
  },
  {
    id: "ZA",
    text: "South Africa",
  },
  {
    id: "GS",
    text: "South Georgia And Sandwich Isl.",
  },
  {
    id: "ES",
    text: "Spain",
  },
  {
    id: "LK",
    text: "Sri Lanka",
  },
  {
    id: "SD",
    text: "Sudan",
  },
  {
    id: "SR",
    text: "Suriname",
  },
  {
    id: "SJ",
    text: "Svalbard And Jan Mayen",
  },
  {
    id: "SZ",
    text: "Swaziland",
  },
  {
    id: "SE",
    text: "Sweden",
  },
  {
    id: "CH",
    text: "Switzerland",
  },
  {
    id: "SY",
    text: "Syrian Arab Republic",
  },
  {
    id: "TW",
    text: "Taiwan",
  },
  {
    id: "TJ",
    text: "Tajikistan",
  },
  {
    id: "TZ",
    text: "Tanzania",
  },
  {
    id: "TH",
    text: "Thailand",
  },
  {
    id: "TL",
    text: "Timor-Leste",
  },
  {
    id: "TG",
    text: "Togo",
  },
  {
    id: "TK",
    text: "Tokelau",
  },
  {
    id: "TO",
    text: "Tonga",
  },
  {
    id: "TT",
    text: "Trinidad And Tobago",
  },
  {
    id: "TN",
    text: "Tunisia",
  },
  {
    id: "TR",
    text: "Turkey",
  },
  {
    id: "TM",
    text: "Turkmenistan",
  },
  {
    id: "TC",
    text: "Turks And Caicos Islands",
  },
  {
    id: "TV",
    text: "Tuvalu",
  },
  {
    id: "UG",
    text: "Uganda",
  },
  {
    id: "UA",
    text: "Ukraine",
  },
  {
    id: "AE",
    text: "United Arab Emirates",
  },
  {
    id: "GB",
    text: "United Kingdom",
  },
  {
    id: "US",
    text: "United States",
  },
  {
    id: "UM",
    text: "United States Outlying Islands",
  },
  {
    id: "UY",
    text: "Uruguay",
  },
  {
    id: "UZ",
    text: "Uzbekistan",
  },
  {
    id: "VU",
    text: "Vanuatu",
  },
  {
    id: "VE",
    text: "Venezuela",
  },
  {
    id: "VN",
    text: "Viet Nam",
  },
  {
    id: "VG",
    text: "Virgin Islands}, British",
  },
  {
    id: "VI",
    text: "Virgin Islands}, U.S.",
  },
  {
    id: "WF",
    text: "Wallis And Futuna",
  },
  {
    id: "EH",
    text: "Western Sahara",
  },
  {
    id: "YE",
    text: "Yemen",
  },
  {
    id: "ZM",
    text: "Zambia",
  },
  {
    id: "ZW",
    text: "Zimbabwe",
  },
];

// SELECTS

function formatCountry(country) {
  if (!country.id) {
    return country.text;
  }
  var $country = $(
    '<span class="form_flag flag-icon flag-icon-' +
      country.id.toLowerCase() +
      '"></span>' +
      '<span class="form_country">' +
      country.text +
      "</span>"
  );
  return $country;
}

$(".form_select").select2({
  placeholder: "Select",
  minimumResultsForSearch: -1,
});

$(".form_select[name='country']").select2({
  placeholder: "Select a country",
  templateResult: formatCountry,
  templateSelection: formatCountry,
  data: isoCountries,
});

$(".graph_select")
  .select2({
    minimumResultsForSearch: -1,
  })
  .on("select2:open", () => {
    setTimeout(() => {
      $(".select2-container--open").addClass("graph_select_container");
    }, 0);
  });

// DROPZONES

$("#photo-dropzone").dropzone({
  url: $(".sign_dropzone").data("url"),
  acceptedFiles: "image/*",
  thumbnailWidth: 186,
  thumbnailHeight: 95,
  autoProcessQueue: false,
  previewTemplate: $("#preview-template").html(),
  previewsContainer: "#dropzone-preview-container",
  init: function () {
    this.on("addedfile", function (file) {
      $(".sign_dropzone_upload").css("display", "none");
      $(".sign_dropzone_remove").css("display", "block");
      $(".form_dropzone_input").val(file.name);
      $('.form_dropzone_file')[0].files = $('.dz-hidden-input')[0].files;
    });

    const _this = this;

    $(document).on({
      dragenter: (e) => {
        e.preventDefault();
        $(".sign_dropzone_alert").css("display", "flex");
      },
      dragleave: (e) => {
        if (!e.relatedTarget) {
          $(".sign_dropzone_alert").hide();
        }
      },
    });

    $(window).on("blur", () => {
      $(".sign_dropzone_alert").hide();
    });

    $(".sign_dropzone_alert").on({
      dragover: (e) => {
        e.preventDefault();
      },
      drop: (e) => {
        const file = e.originalEvent.dataTransfer.files[0];
        _this.addFile(file);
        $(".sign_dropzone_alert").hide();
        return false;
      },
    });

    $("#dropzone-photo-remove").on("click", () => {
      _this.removeAllFiles();
      $(".form_dropzone_input").val("");
      $(".sign_dropzone_upload").css("display", "block");
      $(".sign_dropzone_remove").css("display", "");
    });
  },
});

$("#logo-dropzone").dropzone({
  url: $(".main_dropzone").data("url"),
  acceptedFiles: "image/*",
  thumbnailWidth: 121,
  thumbnailHeight: 62,
  autoProcessQueue: false,
  previewTemplate: $("#preview-template").html(),
  previewsContainer: "#dropzone-preview-container",
  init: function () {
    this.on("addedfile", function (file) {
      $('#dropzone-preview-container [data-dz-thumbnail]').not(':last').remove();
      $(".main_dropzone_remove").css("display", "block");
      $(".form_dropzone_input").val(file.name);
      $('.form_dropzone_file')[0].files = $('.dz-hidden-input')[0].files;
    });

    const _this = this;

    $(document).on({
      dragenter: (e) => {
        e.preventDefault();
        $(".main_dropzone_alert").css("display", "flex");
      },
      dragleave: (e) => {
        if (!e.relatedTarget) {
          $(".main_dropzone_alert").hide();
        }
      },
    });

    $(window).on("blur", () => {
      $(".main_dropzone_alert").hide();
    });

    $(".main_dropzone_alert").on({
      dragover: (e) => {
        e.preventDefault();
      },
      drop: (e) => {
        const file = e.originalEvent.dataTransfer.files[0];
        _this.addFile(file);
        $(".main_dropzone_alert").hide();
        return false;
      },
    });

    $("#dropzone-photo-remove").on("click", () => {
      _this.removeAllFiles();
      $(".form_dropzone_input").val("");
      $(".main_dropzone_remove").css("display", "");
    });
  },
});

// OVERLAY

if ($(".main_overlay").hasClass("visible")) {
  $("body").css("overflow", "hidden");
} else {
  $("body").css("overflow", "");
}

// BURGER MENU

$(".header_menu_open").on("click", () => {
  $(".menu").addClass("-open");
  $("body").css("overflow", "hidden");
});

$(".header_menu_close").on("click", () => {
  $(".menu").removeClass("-open");
  if ($(".main_overlay").hasClass("visible")) {
    $("body").css("overflow", "hidden");
  } else {
    $("body").css("overflow", "");
  }
});

// TABLE SORT

$(".sorting_table_item").on("click", function () {
  const table = $(this).closest("table");
  const order = $(this).attr("data-order");
  const rows = $(table).find("tr:not(:first-child,.table_bg)");
  let newOrder;
  if (order === "up") {
    newOrder = "down";
  } else {
    newOrder = "up";
  }
  setNewOrder(this, newOrder);
  const newRows = sortTable(rows, this, newOrder);
  $(rows).remove();
  $(table).append(newRows);
});

function setNewOrder(item, order) {
  $(".sorting_table_item").attr("data-order", "upDown");
  $(".sorting_table_item")
    .find("img")
    .attr(
      "src",
      $(".sorting_table_item")
        .find("img")
        .attr("src")
        .replace(/\-[a-zA-z]+\.svg/, "-upDown.svg")
    );
  const arrow = $(item).find("img");
  $(item).attr("data-order", order);
  arrow.attr(
    "src",
    arrow.attr("src").replace(/\-[a-zA-z]+\.svg/, `-${order}.svg`)
  );
}

function sortTable(rows, column, order) {
  const table = $(column).closest("table");
  const index = $(table).find("tr:first-child td").index($(column));
  const dataType = $(column).attr("data-type");
  const newRows = [...rows];
  return newRows.sort((a, b) => {
    const aVal = $($(a).find("td")[index]).text();
    const bVal = $($(b).find("td")[index]).text();
    if (order === "up") {
      if (dataType === "text") {
        return aVal.localeCompare(bVal);
      } else if (dataType === "number") {
        return +aVal - +bVal;
      } else if (dataType === "date") {
        const aArr = aVal.trim().split(" ");
        const aDate = aArr[0].split("/");
        const aTime = aArr[1].split(":");
        const aDateTime = new Date(
          aDate[2],
          aDate[1] - 1,
          aDate[0],
          aTime[0],
          aTime[1]
        ).toString();
        const bArr = bVal.trim().split(" ");
        const bDate = bArr[0].split("/");
        const bTime = bArr[1].split(":");
        const bDateTime = new Date(
          bDate[2],
          bDate[1] - 1,
          bDate[0],
          bTime[0],
          bTime[1]
        );
        return new Date(aDateTime) - new Date(bDateTime);
      }
    } else {
      if (dataType === "text") {
        return bVal.localeCompare(aVal);
      } else if (dataType === "number") {
        return +bVal - +aVal;
      } else if (dataType === "date") {
        const aArr = aVal.trim().split(" ");
        const aDate = aArr[0].split("/");
        const aTime = aArr[1].split(":");
        const aDateTime = new Date(
          aDate[2],
          aDate[1] - 1,
          aDate[0],
          aTime[0],
          aTime[1]
        ).toString();
        const bArr = bVal.trim().split(" ");
        const bDate = bArr[0].split("/");
        const bTime = bArr[1].split(":");
        const bDateTime = new Date(
          bDate[2],
          bDate[1] - 1,
          bDate[0],
          bTime[0],
          bTime[1]
        );
        return new Date(bDateTime) - new Date(aDateTime);
      }
    }
  });
}

// GRAPH


const graphCanvas = document.getElementById("graph");
if (graphCanvas) {
  $('.graph_select').on('change', (e) => {
    let labels = $('.graph_section').data($(e.currentTarget).val()).labels;
    let graphData = $('.graph_section').data($(e.currentTarget).val()).graphData;
    if(this.stackedLine)
    {
      this.stackedLine.destroy();
    }
    const ctx = graphCanvas.getContext("2d");
    const width = $(".graph_section_graph").width();
    let gradientFill = ctx.createLinearGradient(0, 0, width, 0);
    gradientFill.addColorStop(0, "#6FCC1808");
    gradientFill.addColorStop(1, "#05BBC908");
    let gradientFill2 = ctx.createLinearGradient(0, 0, width, 0);
    gradientFill2.addColorStop(0, "#6FCC18");
    gradientFill2.addColorStop(1, "#05BBC9");
    const data = {
      labels: labels,
      datasets: [
        {
          data: graphData,
          tension: 0.25,
          borderColor: gradientFill2,
          fill: true,
          backgroundColor: gradientFill,
          pointBackgroundColor: "transparent",
          pointHitRadius: 20,
        },
      ],
    };

    let stepSize = 20;

    this.stackedLine = new Chart(ctx, {
      type: "line",
      data: data,
      options: {
        elements: {
          point: {
            radius: 0.5,
            hoverRadius: 6,
            hoverBorderColor: "#ffffff",
            hoverBackgroundColor: "inherit",
            hoverBorderWidth: 7,
          },
        },
        plugins: {
          legend: {
            display: false,
          },
          responsive: true,
          scale: {
            pointLabels: {
              weight: "bold",
            },
          },
          tooltip: {
            bodyFont: {
              weight: "700",
            },
            callbacks: {
              label: (item) => {
                return item.formattedValue;
              },
              title: () => {
                return "";
              },
            },
            displayColors: false,
            padding: {
              x: 8,
              y: 6,
            },
            titleMarginBottom: 0,
            xAlign: "center",
            yAlign: "bottom",
          },
        },
        scales: {
          x: {
            grid: {
              display: false,
            },
            ticks: {
              padding: 20,
            },
          },
          y: {
            beginAtZero: true,
            grid: {
              display: true,
              drawBorder: false,
              drawOnChartArea: true,
              drawTicks: true,
              tickBorderDash: [5, 5],
            },
            max:
                Math.ceil(
                    (graphData.reduce((a, b) => Math.max(a, b)) + 10) / stepSize
                ) * stepSize,
            ticks: {
              stepSize: stepSize,
              padding: 20,
            },
          },
        },
      },
    });
    Chart.defaults.font.family = "Inter";
    Chart.defaults.font.size = "12";
    Chart.defaults.font.weight = "400";
    Chart.defaults.font.lineHeight = "1.5";

    $(window).on("resize", () => {
      const width = $(".graph_section_graph").width();
      let gradientFill = ctx.createLinearGradient(0, 0, width, 0);
      gradientFill.addColorStop(0, "#6FCC1808");
      gradientFill.addColorStop(1, "#05BBC908");
      let gradientFill2 = ctx.createLinearGradient(0, 0, width, 0);
      gradientFill2.addColorStop(0, "#6FCC18");
      gradientFill2.addColorStop(1, "#05BBC9");
      this.stackedLine.data.datasets[0].backgroundColor = gradientFill;
      this.stackedLine.data.datasets[0].borderColor = gradientFill2;
      $("#graph").css({ width: "100%", height: "auto" });
    });
  })
  $('.graph_select').trigger('change');
}

// FORM VALIDATION

let formErrors = [];
const form = $(".form");
if ($(form).length > 0) {
  $(form).on("submit", function (e) {
    if($(this).hasClass('-novalidate'))
      return;
    const errors = $(this).find(".-error");
    $.each(errors, (index, item) => {
      $(item).removeClass("-error");
    });
    formErrors = [];
    validateForm(this);
    if(formErrors.length !== 0)
      e.preventDefault();
  });
}

const validateForm = (form) => {
  const fields = $(form).find("input, select");
  $.each(fields, (index, item) => {
    if ($(item).is("[required]")) {
      checkRequired(item);
    }
    if ($(item).attr("type") === "email") {
      checkEmail(item);
    }
    if ($(item).attr("type") === "password") {
      checkPassword(item);
    }
  });
  if (formErrors.length > 0) {
    flashErrors();
  }
}

function checkRequired(node) {
  if ($(node).is("input")) {
    const type = $(node).attr("type");
    if (type === "checkbox" && $(node).is(":not(:checked)")) {
      formErrors.push(node);
    } else if ($(node).val().length === 0) {
      formErrors.push(node);
    }
  } else if ($(node).is("select") && $(node).val().length === 0) {
    formErrors.push(node);
  }
}

function checkEmail(node) {
  const regex = /^[a-z\d]+[\w\d.-]*@(?:[a-z\d]+[a-z\d-]+\.){1,5}[a-z]{2,6}$/i;
  if (!regex.test($(node)[0].value.trim())) {
    formErrors.push(node);
  }
}

function checkPassword(node) {
  const regex = /^(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{7,14}$/;
  if (!regex.test($(node)[0].value.trim())) {
    formErrors.push(node);
  }
}

function flashErrors() {
  const errors = new Set([...formErrors]);
  errors.forEach((item) => {
    if ($(item).is("input")) {
      const type = $(item).attr("type");
      if (type === "checkbox") {
        const label = $(item).siblings(".form_checkbox_label");
        $(label).addClass("-error");
      } else if (type === "dropzone") {
        const label = $(item).siblings(".form_label");
        const dropzone = $(item).siblings(".my_dropzone");
        $(dropzone).addClass("-error");
        $(label).addClass("-error");
      } else {
        const label = $(item).siblings(".form_label");
        const formBox = $(item).closest(".form_control");
        $(item).addClass("-error");
        $(label).addClass("-error");
        $(formBox).addClass("-error");
      }
    } else if ($(item).is("select")) {
    }
  });
}

$(document).ready(() => {
  $('[data-source]').each(function(){
    let that = this;
    const urlParams = new URLSearchParams(window.location.search);
   $.ajax({
      method: 'GET',
      url: '/wp-admin/admin-ajax.php',
      data: {
        action : 'tm_load_data',
        component: $(this).data('source'),
        program_id: urlParams.get('program_id')
      },
      success: function(response){
         $(that).removeClass('-loading');
         $(that).html(response)
      }
    })
  })
})