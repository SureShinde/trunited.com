Product.Config.prototype.reloadPrice = 
	Product.Config.prototype.reloadPrice.wrap(function(parentMethod){
	
	   if (this.config.disablePriceReload) {
            return;
        }
        var price    = 0;
        var oldPrice = 0;
        for(var i=this.settings.length-1;i>=0;i--){
            var selected = this.settings[i].options[this.settings[i].selectedIndex];
            if(selected.config){
                price    += parseFloat(selected.config.price);
                oldPrice += parseFloat(selected.config.oldPrice);
            }
        }

        optionsPrice.changePrice('config', {'price': price, 'oldPrice': oldPrice});
        optionsPrice.reload();
		/* Start on 29-04-2016 : Add function for display product price with term and custom option price */
		updateProductPrice();
		/* Start on 29-04-2016 : Add function for display product price with term and custom option price */
        return price;

        if($('product-price-'+this.config.productId)){
            $('product-price-'+this.config.productId).innerHTML = price;
        }
        this.reloadOldPrice();
});


