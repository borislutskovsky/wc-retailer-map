(function(){
  "use strict";

  angular.module('wcRetailersMap', [])
    .controller('wcRetailersMapController', wcRetailersMapController);

  wcRetailersMapController.$inject = ['$http'];

  function wcRetailersMapController(http){
    var vm = this;
    vm.key = 'AIzaSyBzFE6DQQT4sSPaKPkKimJlrpu_FN3WDco';
    vm.geocodeLocation = function(){
      var qryString = [vm.address, vm.city, vm.state, vm.postal_code, vm.country].join('+');
      http({
        method: 'GET',
        url: 'https://maps.googleapis.com/maps/api/geocode/json?address=' + qryString + '&key=' + vm.key
      }).then(function(response){
        var location = response.data.results[0];
        if(location.geometry != undefined){
          vm.lat = location.geometry.location.lat;
          vm.long = location.geometry.location.lng;
        }
      });
    }
  }
})();
