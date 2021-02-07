/***Handle form validation on submit***/

RES_FORM.onsubmit = function (e) {
    let barcodeInpts = document.querySelectorAll(".barcode-input");
    let errorMessage =
        "Please try again. <br><ul><li>Barcodes must be 14 degits long</li><li>Barcodes must be different</li><ul>";
    if (!checkUniqueBarcode(barcodeInpts) || !checkBarcodeLength()) {
        e.preventDefault();
        FORM_ERROR.innerHTML = errorMessage;
    }
}

function checkUniqueBarcode(barcodeInpts) {
    let barcode0 = barcodeInpts[0].value.trim();
    let barcode1 = barcodeInpts[1].value.trim();
    let barcode2 = barcodeInpts[2].value.trim();

    if (barcode0 === barcode1 || barcode0 === barcode2 || barcode1 === barcode2) {
        return false;
    } else {
        return true;
    }
}

function checkBarcodeLength() {
    //Barcodes must be different
    for (let i = 0; i < barcodeInpts.length; i++) {
        if (barcodeInpts[i].value.length !== BARCODE_LENGTH) {
            return false;
        } else {
            return true;
        }
    }
}