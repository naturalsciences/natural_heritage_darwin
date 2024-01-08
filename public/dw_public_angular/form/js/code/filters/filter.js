var darwinFilters=angular.module('darwinFilters', []);

darwinFilters.filter('trustAsResourceUrl', ['$sce', function($sce) {
    return function(val) {
        return $sce.trustAsResourceUrl(val);
    };
}]);

darwinFilters.filter('range', function() {
  return function(input, total) {
    total = parseInt(total);

    for (var i=0; i<=total; i++) {
      input.push(i);
    }

    return input;
  };
});

darwinFilters.filter('range_root', function() {
  return function(input, root, total) {
    total = parseInt(total);

    for (var i=root; i<=total; i++) {
      input.push(i);
    }

    return input;
  };
});

darwinFilters.filter('range_year', function() {
  return function(input, root, total) {
    total = parseInt(total);

    for (var i=1700; i<=new Date().getFullYear(); i++) {
      input.push(i);
    }

    return input;
  };
});

darwinFilters.filter('reverse', function() {
  return function(items) {
    return items.slice().reverse();
  };
});