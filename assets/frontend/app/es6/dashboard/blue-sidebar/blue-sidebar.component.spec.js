'use strict';

describe('Component: blueSidebar', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let scope;
  let element;
  let $state;
  let $rootScope;
  let $stateParams;
  let blueSidebarDatasource;
  let $q;
  let $compile;

  const template = '<blue-sidebar record-type="leads" id="1337"></blue-sidebar>';

  beforeEach(inject(function (_$rootScope_, _$compile_, _$q_, _$state_, _blueSidebarDatasource_, _$stateParams_) {
    $rootScope = _$rootScope_;
    $state = _$state_;
    $q = _$q_;
    $compile = _$compile_;
    $stateParams = _$stateParams_;
    blueSidebarDatasource = _blueSidebarDatasource_;

    mockHelpers.blockUIRouter($state);
  }));

  function compile(stateName, responseOverrides = {}, resolveResponse = true) {
    if (resolveResponse) {
      const defaultResponse = {
        "title": "COMPANY",
        "titleContact": "CONTACT PERSON",
        "name": "Colruyt Group",
        "number": "12345",
        "id": "123456789",
        "type": "FUTURE CUSTOMER",
        "record_type": "B2B",
        "street": "Kroonstraat",
        "houseNumber": "81",
        "bus": "1",
        "addition": "b",
        "postalCode": "3020",
        "city": "Herent",
        "enterprise_number": "BE123456789",
        "first_name": "Annemie",
        "last_name": "Van Den Broecke",
        "function": "Chief financial officer",
        "phone": "0032 15 316124",
        "mobile": "0032 486326874",
        "e-mail": "annemie@colruyt.be",
        "birthdate": "04-04-1986",
        "language": "Dutch",
        "contracts_elec": "3",
        "contracts_gas": "9",
        "arrowLink": {
          "title": "Dashboard",
          "linkTo": "dashboard",
          "params": {
            "mainMenuKey": "sales",
            "dashboardId": "1337"
          }
        },
        "buttons": [{
          "title": "Company",
          "icon": "icon-bedrijf",
          "linkTo": "focus-mode",
          "params": {
            "mainMenuKey": "sales",
            "focusModeId": "1338"
          }
        }, {
          "title": "Quotes",
          "icon": "icon-quote",
          "linkTo": "dashboard",
          "params": {
            "mainMenuKey": "marketing",
            "dashboardId": "1339"
          }
        }],
        "contractTotalLink": {
          "icon": "icon-log",
          "linkTo": "focus-mode",
          "params": {
            "mainMenuKey": "sales-marketing",
            "focusModeId": "account_cockpit_contracts",
            "recordId": "123"
          }
        },
        "messages": [
          "example message 1",
          "example message 2"
        ]
      };

      const response = _.merge({}, defaultResponse, responseOverrides);
      spyOn(blueSidebarDatasource, 'get').and.callFake(mockHelpers.resolvedPromise($q, response));
    } else {
      spyOn(blueSidebarDatasource, 'get').and.returnValue($q.defer().promise);
    }

    $state.current.name = stateName;
    if (stateName === 'dashboard') {
      $stateParams.dashboardId = '1339';
    } else {
      $stateParams.focusModeId = '1338';
    }

    scope = $rootScope.$new();

    element = angular.element(template);
    element = $compile(element)(scope);
    $rootScope.$apply();
  }

  describe('on dashboard B2B', function () {
    beforeEach(function () {
      compile('dashboard');

      expect(blueSidebarDatasource.get).toHaveBeenCalledTimes(1);
      expect(blueSidebarDatasource.get).toHaveBeenCalledWith({ recordType: "leads", id: "1337" });
    });

    it('should create a blue bar card with information about the company.', function () {
      expect(element.find('h1').text()).toBe("Colruyt Group");

      const h3Elements = element.find('h3');
      const titleElement = $(h3Elements[0]);
      expect(titleElement.text()).toContain('COMPANY');

      const titleContactElement = $(h3Elements[1]);
      expect(titleContactElement.text()).toContain('CONTACT PERSON');

      const h4Elements = element.find('h4');
      const accountNumberElement = $(h4Elements[0]);
      expect(accountNumberElement.text()).toContain('12345');

      const paragraphElements = element.find('p');
      expect(paragraphElements.length).toBe(5);

      const companyParagraphElement = $(paragraphElements[0]);
      expect(companyParagraphElement.text()).toContain('Kroonstraat');
      expect(companyParagraphElement.text()).toContain('81');
      expect(companyParagraphElement.text()).toContain('b');
      expect(companyParagraphElement.text()).toContain('3020');
      expect(companyParagraphElement.text()).toContain('Herent');

      const vatParagraphElement = $(paragraphElements[1]);
      expect(vatParagraphElement.text()).toContain('BE123456789');

      const personParagraphElement = $(paragraphElements[2]);
      expect(personParagraphElement.text()).toContain('Annemie');
      expect(personParagraphElement.text()).toContain('Van Den Broecke');
      expect(personParagraphElement.text()).toContain('Chief financial officer');

      const contactParagraphElement = $(paragraphElements[3]);
      expect(contactParagraphElement.text()).toContain('0032 15 316124');
      expect(contactParagraphElement.text()).toContain('0032 486326874');
      expect(contactParagraphElement.text()).toContain('annemie@colruyt.be');

      const languageParagraphElement = $(paragraphElements[4]);
      expect(languageParagraphElement.text()).toContain('Dutch');

      const spanElements = element.find('span');
      const elecContractCount = spanElements[7];
      expect($(elecContractCount).html()).toContain('3');

      const gasContractCount = spanElements[9];
      expect($(gasContractCount).html()).toContain('9');
    });

    it('should create a blue bar with an arrow navigation', function () {
      const arrowLinkElement = $(element.find('a.nav-header')[0]);

      expect(arrowLinkElement.text()).toContain('Dashboard');

      arrowLinkElement.click();
      $rootScope.$apply();

      expect($state.go).toHaveBeenCalledTimes(1);
      expect($state.go).toHaveBeenCalledWith('dashboard', { mainMenuKey: "sales", dashboardId: "1337" });
    });

    it('should create a blue bar with navigation items', function () {
      const navItemElements = element.find('a.nav-item');
      expect(navItemElements.length).toBe(2);

      const buttonSalesElement = $(navItemElements[0]);
      expect(buttonSalesElement.find('span').hasClass('icon-bedrijf')).toBe(true);
      expect(buttonSalesElement.find('small').text()).toBe('Company');

      buttonSalesElement.click();
      $rootScope.$apply();

      expect($state.go).toHaveBeenCalledTimes(1);
      expect($state.go).toHaveBeenCalledWith('focus-mode', { mainMenuKey: "sales", focusModeId: "1338" });

      const buttonMarketingElement = $(navItemElements[1]);
      expect(buttonMarketingElement.find('span').hasClass('icon-quote')).toBe(true);
      expect(buttonMarketingElement.find('small').text()).toBe('Quotes');

      buttonMarketingElement.click();
      $rootScope.$apply();

      expect($state.go).toHaveBeenCalledTimes(2);
      expect($state.go).toHaveBeenCalledWith('dashboard', { mainMenuKey: "marketing", dashboardId: "1339" });
    });

    it('should navigate to contracts dashboard when contract electricity icon is clicked', function () {
      const navItemElements = element.find('a');

      const contractLinkElec = $(navItemElements[0]);

      contractLinkElec.click();
      $rootScope.$apply();

      expect($state.go).toHaveBeenCalledWith('focus-mode', {
        mainMenuKey: "sales-marketing",
        focusModeId: "account_cockpit_contracts",
        recordId: "123"
      });
    });

    it('should navigate to contracts dashboard when contract gas icon is clicked', function () {
      const navItemElements = element.find('a');

      const contractLinkElec = $(navItemElements[1]);

      contractLinkElec.click();
      $rootScope.$apply();

      expect($state.go).toHaveBeenCalledWith('focus-mode', {
        mainMenuKey: "sales-marketing",
        focusModeId: "account_cockpit_contracts",
        recordId: "123"
      });
    });

    it('should add a class active to the selected item', function () {
      const navItemElements = element.find('a.nav-item');
      expect($(navItemElements[0]).hasClass("active")).toBe(false);
      expect($(navItemElements[1]).hasClass("active")).toBe(true);
    });

    it('should create a blue bar with messages', function () {
      const cardMessageElements = $(element.find('div.card__message'));

      expect($(cardMessageElements[0]).text()).toContain('example message 1');
      expect($(cardMessageElements[1]).text()).toContain('example message 2');
    });
  });

  describe('on dashboard B2C', function () {
    beforeEach(function () {
      compile(
        'dashboard',
        { record_type: 'B2C', name: "Jan Verstraeten", title: "CUSTOMER", titleContact: "CONTACT DETAILS" }
      );

      expect(blueSidebarDatasource.get).toHaveBeenCalledTimes(1);
      expect(blueSidebarDatasource.get).toHaveBeenCalledWith({ recordType: "leads", id: "1337" });
    });

    it('should create a blue bar card with information about the customer.', function () {
      expect(element.find('h1').text()).toBe("Jan Verstraeten");

      const h3Elements = element.find('h3');
      const titleElement = $(h3Elements[0]);
      expect(titleElement.text()).toContain('CUSTOMER');

      const titleContactElement = $(h3Elements[1]);
      expect(titleContactElement.text()).toContain('CONTACT DETAILS');

      const h4Elements = element.find('h4');
      const accountNumberElement = $(h4Elements[0]);
      expect(accountNumberElement.text()).toContain('12345');

      const paragraphElements = element.find('p');
      expect(paragraphElements.length).toBe(3);

      const companyParagraphElement = $(paragraphElements[0]);
      expect(companyParagraphElement.text()).toContain('Kroonstraat');
      expect(companyParagraphElement.text()).toContain('81');
      expect(companyParagraphElement.text()).toContain('b');
      expect(companyParagraphElement.text()).toContain('3020');
      expect(companyParagraphElement.text()).toContain('Herent');

      const contactParagraphElement = $(paragraphElements[1]);
      expect(contactParagraphElement.text()).toContain('0032 15 316124');
      expect(contactParagraphElement.text()).toContain('0032 486326874');
      expect(contactParagraphElement.text()).toContain('annemie@colruyt.be');
      expect(contactParagraphElement.text()).toContain('04-04-1986');

      const languageParagraphElement = $(paragraphElements[2]);
      expect(languageParagraphElement.text()).toContain('Dutch');

      const spanElements = element.find('span');
      const elecContractCount = spanElements[6];
      expect($(elecContractCount).html()).toContain('3');

      const gasContractCount = spanElements[8];
      expect($(gasContractCount).html()).toContain('9');
    });
  });

  describe('on dashboard B2C without contact details', function () {
    beforeEach(function () {
      compile(
        'dashboard',
        { record_type: 'B2C', phone: '', mobile: '' }
      );

      expect(blueSidebarDatasource.get).toHaveBeenCalledTimes(1);
      expect(blueSidebarDatasource.get).toHaveBeenCalledWith({ recordType: "leads", id: "1337" });
    });

    it('should create a blue bar card with information about the customer with no contact details.', function () {
      const paragraphElements = element.find('p');
      expect(paragraphElements.length).toBe(3);

      const companyParagraphElement = $(paragraphElements[0]);
      expect(companyParagraphElement.text()).toContain('Kroonstraat');
      expect(companyParagraphElement.text()).toContain('81');
      expect(companyParagraphElement.text()).toContain('b');
      expect(companyParagraphElement.text()).toContain('3020');
      expect(companyParagraphElement.text()).toContain('Herent');

      const contactParagraphElement = $(paragraphElements[1]);
      expect(contactParagraphElement.text()).not.toContain('0032 15 316124');
      expect(contactParagraphElement.text()).not.toContain('0032 486326874');
      expect(contactParagraphElement.text()).toContain('annemie@colruyt.be');
      expect(contactParagraphElement.text()).toContain('04-04-1986');

      const languageParagraphElement = $(paragraphElements[2]);
      expect(languageParagraphElement.text()).toContain('Dutch');

      const spanElements = element.find('span');
      const elecContractCount = spanElements[4];
      expect($(elecContractCount).html()).toContain('3');

      const gasContractCount = spanElements[6];
      expect($(gasContractCount).html()).toContain('9');
    });
  });

  describe('icon behavior', function () {
    it('should render no icon when data is undefined', function () {
      compile('dashboard', {}, false);

      const customerStatus = $(element.find('.customer__status')[0]);
      expect(customerStatus.attr('class')).toBe(undefined);
    });

    it('should render a "icon-particulier" when record_type is B2C"', function () {
      compile('dashboard', { record_type: 'B2C', type: '' });

      const customerStatus = $(element.find('.customer__status')[0]);
      expect(customerStatus.attr('class')).toBe('customer__status icon-particulier');
    });

    it('should render a "icon-bedrijf" when record_type is B2B"', function () {
      compile('dashboard', { record_type: 'B2B', type: '' });

      const customerStatus = $(element.find('.customer__status')[0]);
      expect(customerStatus.attr('class')).toBe('customer__status icon-bedrijf');
    });

    it('should when record_type is B2B and type is "FUTURE CUSTOMER" render "status-plus"', function () {
      compile('dashboard', { record_type: 'B2B', type: "FUTURE CUSTOMER" });

      const customerStatus = $(element.find('.customer__status')[0]);
      expect(customerStatus.attr('class')).toBe('customer__status icon-bedrijf status-plus');
    });

    it('should when record_type is B2B and type is "OLD CUSTOMER" render "status-old"', function () {
      compile('dashboard', { record_type: 'B2B', type: "OLD CUSTOMER" });

      const customerStatus = $(element.find('.customer__status')[0]);
      expect(customerStatus.attr('class')).toBe('customer__status icon-bedrijf status-old');
    });

    it('should when record_type is B2B and type is "PROSPECT" render "status-star"', function () {
      compile('dashboard', { record_type: 'B2B', type: "PROSPECT" });

      const customerStatus = $(element.find('.customer__status')[0]);
      expect(customerStatus.attr('class')).toBe('customer__status icon-bedrijf status-star');
    });

    it('should when record_type is B2C and type is "FUTURE CUSTOMER" render "status-plus"', function () {
      compile('dashboard', { record_type: 'B2C', type: "FUTURE CUSTOMER" });

      const customerStatus = $(element.find('.customer__status')[0]);
      expect(customerStatus.attr('class')).toBe('customer__status icon-particulier status-plus');
    });

    it('should when record_type is B2C and type is "OLD CUSTOMER" render "status-old"', function () {
      compile('dashboard', { record_type: 'B2C', type: "OLD CUSTOMER" });

      const customerStatus = $(element.find('.customer__status')[0]);
      expect(customerStatus.attr('class')).toBe('customer__status icon-particulier status-old');
    });

    it('should when record_type is B2C and type is "PROSPECT" render "status-star"', function () {
      compile('dashboard', { record_type: 'B2C', type: "PROSPECT" });

      const customerStatus = $(element.find('.customer__status')[0]);
      expect(customerStatus.attr('class')).toBe('customer__status icon-particulier status-star');
    });
  });

  describe('on focus-mode', function () {
    beforeEach(function () {
      compile('focus-mode');
    });

    it('should add a class active to the selected item', function () {
      const navItemElements = element.find('a.nav-item');
      expect($(navItemElements[0]).hasClass("active")).toBe(true);
      expect($(navItemElements[1]).hasClass("active")).toBe(false);
    });
  });
});
