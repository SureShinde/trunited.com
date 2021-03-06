var isEE = (navigator.userAgent.indexOf("MSIE") != false);
AwEbdPosition = Class.create();
AwEbdPosition.prototype = {
    initialize: function () {
        this.inputs = [];
        this.buttons = [];
        this.positions = [];
        this.values = [];
        this.selectboxes = [];
    },
    register: function (container, button, input, selectbox) {
        this.buttons.push(button);
        selectbox.selectors = [];
        this.selectboxes.push(selectbox);
        this.inputs.push(input);
        container.observe('mouseover', (function (event) {
            showElement(button);
        }).bind(button));
        container.observe('mouseout', (function (event) {
            hideElement(button);
        }).bind(button));
        /* Click on button */
        button.observe('click', (function (event) {
            if (isIE) {
                selectbox.style.left = (event.offsetX - 10) + 'px';
                selectbox.style.top = (event.offsetY - 10) + 'px';
            } else {
                selectbox.style.left = (event.layerX - 10) + 'px';
                selectbox.style.top = (event.layerY - 10) + 'px';
            }
            showElement(selectbox);
            this.closeAllBoxes(selectbox);
        }).bind(this).bind(selectbox).bind(input).bind(button));
        /* Click somethere */
        document.observe('click', (function (event) {
            if (this.isSelectbox(event.target.id)) {
                return false;
            }
            if (this.isButton(event.target.id)) {
                return false;
            }
            this.closeAllBoxes();
        }).bind(this));
        document.observe('keyup', (function (event) {
            if (event.keyCode == '27') {
                this.closeAllBoxes();
            }
        }).bind(this));
    },
    getSelectbox: function (id) {
        if (this.selectboxes.length > 0) {
            for (var i = 0; i < this.selectboxes.length; i++) {
                if (this.selectboxes[i].id == id) {
                    return this.selectboxes[i];
                }
            }
        }
        return false;
    },
    getSelector: function (selectbox_id, selector_id) {
        var selectbox = this.getSelectbox(selectbox_id);
        if (selectbox != false) {
            if (selectbox.selectors.length > 0) {
                for (var i = 0; i < selectbox.selectors.length; i++) {
                    if (selectbox.selectors[i].id == selector_id) {
                        return selectbox.selectors[i];
                    }
                }
            }
        }
        return false;
    },
    deactivateSelectors: function (selectbox_id) {
        var selectbox = this.getSelectbox(selectbox_id);
        if (selectbox != false) {
            if (selectbox.selectors.length > 0) {
                for (var i = 0; i < selectbox.selectors.length; i++) {
                    selectbox.selectors[i].removeClassName('active');
                }
            }
        }
        return false;
    },
    registerSelector: function (selectbox, selector, value, input, label) {
        selector.observe('mouseover', (function (event) {
            selector.addClassName('over');
        }).bind(selector));
        selector.observe('mouseout', (function (event) {
            selector.removeClassName('over');
        }).bind(selector));
        selector.observe('click', (function (event) {
            this.setInputValue(input, label, value, selectbox);
        }).bind(this).bind(value).bind(input).bind(label).bind(selectbox));
        this.getSelectbox(selectbox.id).selectors.push(selector);


    },
    isButton: function (element_id) {
        if (this.buttons.length > 0) {
            for (var i = 0; i < this.buttons.length; i++) {
                if (this.buttons[i].id == element_id) {
                    return true;
                }
            }
        }
        return false;
    },
    isSelectbox: function (element_id) {
        if (this.selectboxes.length > 0) {
            for (var i = 0; i < this.selectboxes.length; i++) {
                if (this.selectboxes[i].id == element_id) {
                    return true;
                }
            }
        }
        return false;
    },
    closeAllBoxes: function (except) {
        if (this.selectboxes.length > 0) {
            for (var i = 0; i < this.selectboxes.length; i++) {
                if ((typeof except != 'undefined') && (except.id == this.selectboxes[i].id)) {
                    continue;
                }
                hideElement(this.selectboxes[i]);
            }
        }
    },
    addPosition: function (value, label) {
        this.positions[value] = label;
    },
    setInputValue: function (input, label, value, selectbox) {
        if (typeof input != 'undefined') {
            input.value = value;
            label.value = this.positions[value];

            this.deactivateSelectors(selectbox.id);
            var selector = this.getSelector(selectbox.id, label.id + '_' + value);
            if (selector != false) {
                selector.addClassName('active');
            }
        }
    }
};

var showElement = function (element) {
    if (typeof element != 'undefined') {
        element.style.display = 'block';
    }
};
var hideElement = function (element) {
    if (typeof element != 'undefined') {
        element.style.display = 'none';
    }
};

/* init awPosition */
if(typeof awPosition=='undefined') {
    var awPosition = new AwEbdPosition();
}

var focusNextInput = function (input) {
    var id = input.id;
    id = id.replace('_position', '_image');
    $(id).focus();
};