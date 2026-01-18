/*
 *  jquery-maskmoney - v3.1.1
 *  jQuery plugin to mask data entry in the input text in the form of money.
 *  https://github.com/plentz/jquery-maskmoney
 *
 *  Made by Diego Plentz
 *  Under MIT License
 */
(function ($) {
    "use strict";
    if (!$.browser) {
        $.browser = {};
        $.browser.mozilla = /mozilla/.test(navigator.userAgent.toLowerCase()) && !/webkit/.test(navigator.userAgent.toLowerCase());
        $.browser.webkit = /webkit/.test(navigator.userAgent.toLowerCase());
        $.browser.opera = /opera/.test(navigator.userAgent.toLowerCase());
        $.browser.msie = /msie/.test(navigator.userAgent.toLowerCase());
        $.browser.device = /android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase());
    }

    var defaultOptions = {
            prefix: "",
            suffix: "",
            affixesStay: true,
            thousands: ",",
            decimal: ".",
            precision: 2,
            allowZero: false,
            allowNegative: false,
            doubleClickSelection: true,
            allowEmpty: false,
            bringCaretAtEndOnFocus: true
        },
        methods = {
            destroy: function () {
                $(this).unbind(".maskMoney");

                if ($.browser.msie) {
                    this.onpaste = null;
                }
                return this;
            },

            applyMask: function (value) {
                var $input = $(this);
                // data-* api
                var settings = $input.data("settings");
                return maskValue(value, settings);
            },

            mask: function (value) {
                return this.each(function () {
                    var $this = $(this);
                    if (typeof value === "number") {
                        $this.val(value);
                    }
                    return $this.trigger("mask");
                });
            },

            unmasked: function () {
                return this.map(function () {
                    var value = ($(this).val() || "0"),
                        isNegative = value.indexOf("-") !== -1,
                        decimalPart;
                    // get the last position of the array that is a number(coercion makes "" into 0)
                    $(value.split(/\D/).reverse()).each(function (index, element) {
                        if (element) {
                            decimalPart = element;
                            return false;
                        }
                    });
                    value = value.replace(/\D/g, "");
                    value = value.replace(new RegExp(decimalPart + "$"), "." + decimalPart);
                    if (isNegative) {
                        value = "-" + value;
                    }
                    return parseFloat(value);
                });
            },

            init: function (parameters) {
                parameters = $.extend({}, defaultOptions, parameters);
                return this.each(function () {
                    var $input = $(this), settings,
                        onFocusValue;

                    // data-* api
                    settings = $.extend({}, parameters);
                    settings = $.extend(settings, $input.data());

                    // Store settings for use with the applyMask method.
                    $input.data("settings", settings);


                    function getInputSelection() {
                        var el = $input.get(0),
                            start = 0,
                            end = 0,
                            normalizedValue,
                            range,
                            textInputRange,
                            len,
                            endRange;

                        if (typeof el.selectionStart === "number" && typeof el.selectionEnd === "number") {
                            start = el.selectionStart;
                            end = el.selectionEnd;
                        } else {
                            range = document.selection.createRange();

                            if (range && range.parentElement() === el) {
                                len = el.value.length;
                                normalizedValue = el.value.replace(/\r\n/g, "\n");

                                // Create a working TextRange that lives only in the input
                                textInputRange = el.createTextRange();
                                textInputRange.moveToBookmark(range.getBookmark());

                                // Check if the start and end of the selection are at the very end
                                // of the input, since moveStart/moveEnd doesn't return what we want
                                // in those cases
                                endRange = el.createTextRange();
                                endRange.collapse(false);

                                if (textInputRange.compareEndPoints("StartToEnd", endRange) > -1) {
                                    start = end = len;
                                } else {
                                    start = -textInputRange.moveStart("character", -len);
                                    start += normalizedValue.slice(0, start).split("\n").length - 1;

                                    if (textInputRange.compareEndPoints("EndToEnd", endRange) > -1) {
                                        end = len;
                                    } else {
                                        end = -textInputRange.moveEnd("character", -len);
                                        end += normalizedValue.slice(0, end).split("\n").length - 1;
                                    }
                                }
                            }
                        }

                        return {
                            start: start,
                            end: end
                        };
                    } // getInputSelection

                    function setInputSelection(start, end) {
                        var el = $input.get(0);
                        if (typeof el.selectionStart === "number" && typeof el.selectionEnd === "number") {
                            el.selectionStart = start;
                            el.selectionEnd = end;
                        } else if (typeof el.createTextRange !== "undefined") {
                            var range = el.createTextRange();
                            range.collapse(true);
                            range.moveEnd("character", end);
                            range.moveStart("character", start);
                            range.select();
                        }
                    }

                    function maskAndPosition(startPos) {
                        var originalLen = $input.val().length,
                            newLen;
                        $input.val(maskValue($input.val(), settings));
                        newLen = $input.val().length;
                        // If the we're using the reverse option,
                        // do not put the cursor at the end of
                        // the input. The reverse option allows
                        // the user to input the numbers from
                        // right to left.
                        if (!settings.reverse) {
                            startPos = startPos - (originalLen - newLen);
                        }
                        setInputSelection(startPos, startPos);
                    }

                    function mask() {
                        var value = $input.val();
                        if (settings.allowEmpty && value === "") {
                            return;
                        }
                        var decimalPointIndex = value.indexOf(settings.decimal);
                        if (settings.precision > 0) {
                            if (decimalPointIndex < 0) {
                                value += settings.decimal + new Array(settings.precision + 1).join(0);
                            } else {
                                // If the following decimal part dosen't have enough length against the precision, it needs to be filled with zeros.
                                var integerPart = value.slice(0, decimalPointIndex),
                                    decimalPart = value.slice(decimalPointIndex + 1);
                                value = integerPart + settings.decimal + decimalPart +
                                    new Array((settings.precision + 1) - decimalPart.length).join(0);
                            }
                        } else if (decimalPointIndex > 0) {
                            // if the precision is 0, we are removing all decimal places.
                            value = value.slice(0, decimalPointIndex);
                        }
                        $input.val(maskValue(value, settings));
                    }

                    function changeSign() {
                        var inputValue = $input.val();
                        if (settings.allowNegative) {
                            if (inputValue !== "" && inputValue.charAt(0) === "-") {
                                return inputValue.replace("-", "");
                            } else {
                                return "-" + inputValue;
                            }
                        } else {
                            return inputValue;
                        }
                    }

                    function preventDefault(e) {
                        if (e.preventDefault) { //standard browsers
                            e.preventDefault();
                        } else { // old internet explorer
                            e.returnValue = false;
                        }
                    }

                    function fixMobile() {
                        if ($.browser.device) {
                            $input.attr("type", "tel");
                        }
                    }

                    function keypressEvent(e) {
                        e = e || window.event;
                        var key = e.which || e.charCode || e.keyCode,
                            decimalKeyCode = settings.decimal.charCodeAt(0);
                        //added to handle an IE "special" event
                        if (key === undefined) {
                            return false;
                        }

                        // any key except the numbers 0-9. if we're using settings.reverse,
                        // allow the user to input the decimal key
                        if ((key < 48 || key > 57) && (key !== decimalKeyCode || !settings.reverse)) {
                            return handleAllKeysExceptNumericalDigits(key, e);
                        } else if (key === decimalKeyCode && shouldPreventDecimalKey()) {
                            return false;
                        }
                        return true;
                    }

                    function shouldPreventDecimalKey() {
                        // If we're not allowing decimal points at all
                        if (settings.precision === 0) {
                            return true;
                        }
                        // If we're already past the decimal point
                        var selection = getInputSelection();
                        var decimalPointIndex = $input.val().indexOf(settings.decimal);
                        return selection.start > decimalPointIndex;
                    }

                    function handleAllKeysExceptNumericalDigits(key, e) {
                        // -(minus) key
                        if (key === 45) {
                            $input.val(changeSign());
                            return false;
                            // +(plus) key
                        } else if (key === 43) {
                            $input.val($input.val().replace("-", ""));
                            return false;
                            // enter key or tab key
                        } else if (key === 13 || key === 9) {
                            return true;
                        } else if ($.browser.mozilla && (key === 37 || key === 39) && e.charCode === 0) {
                            // needed for left arrow key or right arrow key with firefox
                            // the charCode part is to avoid allowing "%"(e.charCode 0, e.keyCode 37)
                            return true;
                        } else { // any other key that is not a number
                            preventDefault(e);
                            return false;
                        }
                    }

                    function keydownEvent(e) {
                        e = e || window.event;
                        var key = e.which || e.charCode || e.keyCode;
                        if (key === undefined) {
                            return false;
                        }

                        var selection = getInputSelection();
                        var startPos = selection.start,
                            endPos = selection.end;

                        if (key === 8 || key === 46 || key === 63272) { // backspace or delete key (with special case for safari)
                            preventDefault(e);

                            // If backspace or delete key is pressed and value is already blank, don't do anything
                            if ($input.val() === "" && settings.allowEmpty) {
                                return true;
                            }

                            // If delete key is pressed and selection is at the end of the text, don't do anything
                            if (key === 46 && startPos === endPos && endPos === $input.val().length) {
                                return true;
                            }
                            maskAndPosition(startPos);
                            return false;
                        } else if (key === 9) { // tab key
                            if (settings.allowEmpty && $input.val() === "") {
                                return true;
                            }
                            setInputSelection(0, $input.val().length);
                            return true;
                        } else { // other keys
                            return true;
                        }
                    }

                    function focusEvent() {
                        onFocusValue = $input.val();
                        mask();
                        var input = $input.get(0),
                            textLength = input.value.length;
                        // If the user clicks on a field that already has a value and if we have the configuration
                        // to bring the cursor at the end of the field, do it.
                        if (!!settings.bringCaretAtEndOnFocus) {
                            setInputSelection(textLength, textLength);
                        } else {
                            setInputSelection(0, textLength);
                        }
                    }

                    function blurEvent(e) {
                        if ($.browser.msie) {
                            keypressEvent(e);
                        }

                        if (!!settings.bringCaretAtEndOnFocus) {
                            setInputSelection(0, 0);
                        }

                        if ($input.val() === "" && settings.allowEmpty) {
                            $input.val("");
                        } else if ($input.val() === "" || $input.val() === setSymbol(getDefaultMask(), settings)) {
                            if (!settings.allowZero) {
                                $input.val("");
                            } else if (!settings.affixesStay) {
                                $input.val(getDefaultMask());
                            } else {
                                $input.val(setSymbol(getDefaultMask(), settings));
                            }
                        } else {
                            if (!settings.affixesStay) {
                                var newValue = $input.val().replace(settings.prefix, "").replace(settings.suffix, "");
                                $input.val(newValue);
                            }
                        }
                        if ($input.val() !== onFocusValue) {
                            $input.change();
                        }
                    }

                    function clickEvent() {
                        var input = $input.get(0),
                            length = input.value.length,
                            position = getInputSelection();
                        if (position.start === 0 && position.end === length && !!settings.bringCaretAtEndOnFocus) {
                            setInputSelection(length, length);
                        } else {
                            if (!!settings.doubleClickSelection) {
                                setInputSelection(0, length);
                            }
                        }
                    }

                    fixMobile();
                    $input.unbind(".maskMoney");
                    $input.bind("keypress.maskMoney", keypressEvent);
                    $input.bind("keydown.maskMoney", keydownEvent);
                    $input.bind("blur.maskMoney", blurEvent);
                    $input.bind("focus.maskMoney", focusEvent);
                    $input.bind("click.maskMoney", clickEvent);
                    $input.bind("cut.maskMoney", function () {
                        setTimeout(function () {
                            $input.trigger("mask");
                        }, 0);
                    });
                    $input.bind("paste.maskMoney", function () {
                        setTimeout(function () {
                            $input.trigger("mask");
                        }, 0);
                    });
                    if ($.browser.msie) {
                        $input.bind("dragend.maskMoney", function () {
                            setTimeout(function () {
                                $input.trigger("mask");
                            }, 0);
                        });
                    }
                });
            }
        };

    function setSymbol(value, settings) {
        var operator = "";
        if (value.indexOf("-") > -1) {
            value = value.replace("-", "");
            operator = "-";
        }
        if (value.indexOf(settings.prefix) > -1) {
            value = value.replace(settings.prefix, "");
        }
        if (value.indexOf(settings.suffix) > -1) {
            value = value.replace(settings.suffix, "");
        }
        return operator + settings.prefix + value + settings.suffix;
    }

    function maskValue(value, settings) {
        if (settings.allowEmpty && value === "") {
            return "";
        }
        if (!!settings.reverse) {
            return maskValueReverse(value, settings);
        }
        return maskValueStandard(value, settings);
    }

    function maskValueStandard(value, settings) {
        var negative = (value.indexOf("-") > -1 && settings.allowNegative) ? "-" : "",
            onlyNumbers = value.replace(/[^0-9]/g, ""),
            integerPart = onlyNumbers.slice(0, onlyNumbers.length - settings.precision),
            newValue,
            decimalPart,
            leadingZeros;

        newValue = buildIntegerPart(integerPart, negative, settings);

        if (settings.precision > 0) {
            if (!isNaN(value) && value.indexOf(".")) {
                var precision = value.substr(value.indexOf(".") + 1);
                onlyNumbers = precision.replace(/[^0-9]/g, "");
                decimalPart = onlyNumbers.slice(0, settings.precision);
                leadingZeros = new Array((settings.precision + 1) - decimalPart.length).join(0);
                newValue += settings.decimal + leadingZeros + decimalPart;
            } else {
                newValue += settings.decimal + new Array(settings.precision + 1).join(0);
            }
        }
        return setSymbol(newValue, settings);
    }

    function maskValueReverse(value, settings) {
        var negative = (value.indexOf("-") > -1 && settings.allowNegative) ? "-" : "",
            valueWithoutSymbol = value.replace(settings.prefix, "").replace(settings.suffix, ""),
            integerPart = valueWithoutSymbol.split(settings.decimal)[0],
            newValue,
            decimalPart = "";

        if (integerPart === "") {
            integerPart = "0";
        }
        newValue = buildIntegerPart(integerPart, negative, settings);

        if (settings.precision > 0) {
            var arr = valueWithoutSymbol.split(settings.decimal);
            if (arr.length > 1) {
                decimalPart = arr[1];
            }
            newValue += settings.decimal + decimalPart;
            var rounded = Number.parseFloat((integerPart + "." + decimalPart)).toFixed(settings.precision);
            var roundedDecimalPart = rounded.toString().split(settings.decimal)[1];
            newValue = newValue.split(settings.decimal)[0] + "." + roundedDecimalPart;
        }

        return setSymbol(newValue, settings);
    }

    function buildIntegerPart(integerPart, negative, settings) {
        // remove initial zeros
        integerPart = integerPart.replace(/^0*/g, "");

        // put settings.thousands every 3 chars
        integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, settings.thousands);
        if (integerPart === "") {
            integerPart = "0";
        }
        return negative + integerPart;
    }

    $.fn.maskMoney = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === "object" || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error("Method " + method + " does not exist on jQuery.maskMoney");
        }
    };
})(window.jQuery || window.Zepto); 