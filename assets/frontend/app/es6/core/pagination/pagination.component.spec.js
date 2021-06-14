'use strict';

describe('Component: pagination', function () {
  beforeEach(module('digitalWorkplaceApp'));
  beforeEach(module('digitalWorkplaceAppTemplates'));

  let $rootScope;
  let $compile;

  let element;
  let controller;

  const template = `
    <pagination
      total-pages="controller.totalPages"
      current-page="controller.currentPage"
      page-size="controller.pageSize"
      total-results="controller.totalResults"
      page-changed="controller.setPage(page)">
    </pagination>
  `;

  beforeEach(inject(function (_$rootScope_, _$compile_, $state) {
    mockHelpers.blockUIRouter($state);

    $rootScope = _$rootScope_;
    $compile = _$compile_;
  }));

  function compile({ currentPage, totalPages, pageSize, totalResults }) {
    const scope = $rootScope.$new();

    controller = {
      setPage: jasmine.createSpy(),
      currentPage,
      totalPages,
      pageSize,
      totalResults
    };

    scope.controller = controller;

    element = angular.element(template);
    element = $compile(element)(scope);

    $rootScope.$apply();
  }

  it('should present the user with a text showing the current page status', function () {
    compile({ currentPage: 1, totalPages: 10, pageSize: 10, totalResults: 101 });

    const smallElement = $(element.find('small')[0]);

    expect(smallElement.text()).toBe('showing rows 1 to 10 from 101 results');

    controller.currentPage = 2;
    $rootScope.$apply();

    expect(smallElement.text()).toBe('showing rows 11 to 20 from 101 results');

    controller.currentPage = 9;
    $rootScope.$apply();

    expect(smallElement.text()).toBe('showing rows 81 to 90 from 101 results');

    controller.currentPage = 10;
    $rootScope.$apply();

    expect(smallElement.text()).toBe('showing rows 91 to 100 from 101 results');

    controller.currentPage = 11;
    $rootScope.$apply();

    expect(smallElement.text()).toBe('showing rows 101 to 101 from 101 results');

    controller.totalResults = null;
    controller.currentPage = 5;
    controller.totalPages = 6;
    $rootScope.$apply();

    expect(smallElement.text()).toBe('showing rows 41 to 50');
  });

  it('should render pagination and respond to clicks', function () {
    compile({ currentPage: 15, totalPages: 30, pageSize: 10, totalResults: 300 });

    const liElements = element.find('li');
    expect(liElements.length).toBe(13);

    const prevIconElement = $(element.find('.pagination__prev'));
    expect(prevIconElement.find('.icon-arrow-left').length).toBe(1);
    checkPageClick(prevIconElement, 14);

    const firstPageElement = $(element.find('#first-page'));
    expect(firstPageElement.text()).toContain('1');
    checkPageClick(firstPageElement, 1);

    expect($(element.find('#prev-dots')).text()).toContain('…');

    const prevPageElement3 = $(element.find('#prev-page-3'));
    expect(prevPageElement3.text()).toContain('12');
    checkPageClick(prevPageElement3, 12);

    const prevPageElement2 = $(element.find('#prev-page-2'));
    expect(prevPageElement2.text()).toContain('13');
    checkPageClick(prevPageElement2, 13);

    const prevPageElement1 = $(element.find('#prev-page-1'));
    expect(prevPageElement1.text()).toContain('14');
    checkPageClick(prevPageElement1, 14);

    const currentPageElement = $(element.find('#current-page'));
    expect(currentPageElement.text()).toContain('15');
    expect(currentPageElement.hasClass('is-active')).toBe(true);

    const nextPageElement1 = $(element.find('#next-page-1'));
    expect(nextPageElement1.text()).toContain('16');
    checkPageClick(nextPageElement1, 16);

    const nextPageElement2 = $(element.find('#next-page-2'));
    expect(nextPageElement2.text()).toContain('17');
    checkPageClick(nextPageElement2, 17);

    const nextPageElement3 = $(element.find('#next-page-3'));
    expect(nextPageElement3.text()).toContain('18');
    checkPageClick(nextPageElement3, 18);

    expect($(element.find('#next-dots')).text()).toContain('…');

    const lastPageElement = $(element.find('#last-page'));
    expect(lastPageElement.text()).toContain('30');
    checkPageClick(lastPageElement, 30);

    const nextIconElement = $(element.find('.pagination__next'));
    expect(nextIconElement.find('.icon-arrow-right').length).toBe(1);
    checkPageClick(nextIconElement, 16);
  });

  it('should not display the pagination if we have only one page', function () {
    compile({ currentPage: 1, totalPages: 1, pageSize: 10, totalResults: 2 });
    expect(element.find('ul').length).toBe(0);
  });

  it('should not render the "prev icon" when current-page is one', function () {
    compile({ currentPage: 1, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('.pagination__prev').length).toBe(0);
  });

  it('should not render the "#prev-dots" when rendering close to the first page', function () {
    compile({ currentPage: 6, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#prev-dots').length).toBe(1);

    compile({ currentPage: 5, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#prev-dots').length).toBe(0);

    compile({ currentPage: 4, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#prev-dots').length).toBe(0);

    compile({ currentPage: 3, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#prev-dots').length).toBe(0);

    compile({ currentPage: 2, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#prev-dots').length).toBe(0);

    compile({ currentPage: 1, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#prev-dots').length).toBe(0);
  });

  it('should not render the "#first-page" when rendering close to the first page', function () {
    compile({ currentPage: 5, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#first-page').length).toBe(1);

    // #first-page is useless because #prev-page-3 goes to the first page.
    compile({ currentPage: 4, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#first-page').length).toBe(0);

    // #first-page is useless because #prev-page-2 goes to the first page.
    compile({ currentPage: 3, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#first-page').length).toBe(0);

    // #first-page is useless because #prev-page-1 goes to the first page.
    compile({ currentPage: 2, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#first-page').length).toBe(0);

    // #first-page is useless because we are on the first page
    compile({ currentPage: 1, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#first-page').length).toBe(0);
  });

  it('should not render "#prev-page-3" when there are not 3 prev pages', function () {
    compile({ currentPage: 4, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#prev-page-3').length).toBe(1);

    compile({ currentPage: 3, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#prev-page-3').length).toBe(0);

    compile({ currentPage: 2, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#prev-page-3').length).toBe(0);

    compile({ currentPage: 1, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#prev-page-3').length).toBe(0);
  });

  it('should not render "#prev-page-2" when there are not 2 prev pages', function () {
    compile({ currentPage: 3, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#prev-page-2').length).toBe(1);

    compile({ currentPage: 2, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#prev-page-2').length).toBe(0);

    compile({ currentPage: 1, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#prev-page-2').length).toBe(0);
  });

  it('should not render "#prev-page-1" when there is no previous page', function () {
    compile({ currentPage: 2, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#prev-page-1').length).toBe(1);

    compile({ currentPage: 1, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#prev-page-1').length).toBe(0);
  });

  it('should not render "#next-page-1" when there is no next page', function () {
    compile({ currentPage: 9, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#next-page-1').length).toBe(1);

    compile({ currentPage: 10, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#next-page-1').length).toBe(0);
  });

  it('should not render "#next-page-2" when there are not 2 next pages', function () {
    compile({ currentPage: 8, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#next-page-2').length).toBe(1);

    compile({ currentPage: 9, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#next-page-2').length).toBe(0);

    compile({ currentPage: 10, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#next-page-2').length).toBe(0);
  });

  it('should not render "#next-page-3" when there are not 3 next pages', function () {
    compile({ currentPage: 7, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#next-page-3').length).toBe(1);

    compile({ currentPage: 8, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#next-page-3').length).toBe(0);

    compile({ currentPage: 9, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#next-page-3').length).toBe(0);

    compile({ currentPage: 10, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#next-page-3').length).toBe(0);
  });

  it('should not render the "#next-dots" when rendering close to the last page', function () {
    compile({ currentPage: 5, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#next-dots').length).toBe(1);

    compile({ currentPage: 6, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#next-dots').length).toBe(0);

    compile({ currentPage: 7, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#next-dots').length).toBe(0);

    compile({ currentPage: 8, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#next-dots').length).toBe(0);

    compile({ currentPage: 9, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#next-dots').length).toBe(0);

    compile({ currentPage: 10, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#next-dots').length).toBe(0);
  });

  it('should not render the "#last-page" when rendering close to the last page', function () {
    compile({ currentPage: 6, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#last-page').length).toBe(1);

    // #last-page is useless because #next-page-3 goes to the last page.
    compile({ currentPage: 7, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#last-page').length).toBe(0);

    // #last-page is useless because #next-page-2 goes to the last page.
    compile({ currentPage: 8, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#last-page').length).toBe(0);

    // #last-page is useless because next-icon goes to the last page.
    compile({ currentPage: 9, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#last-page').length).toBe(0);

    // #last-page is useless because we are on the last page
    compile({ currentPage: 10, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('#last-page').length).toBe(0);
  });

  it('should not render the "next icon" when on last page', function () {
    compile({ currentPage: 10, totalPages: 10, pageSize: 10, totalResults: 100 });
    expect(element.find('.pagination__next').length).toBe(0);
  });

  function checkPageClick(element, page) {
    controller.setPage.calls.reset();

    const aHrefElement = $(element.find('a')[0]);

    aHrefElement.click();
    $rootScope.$apply();

    expect(controller.setPage).toHaveBeenCalledTimes(1);
    expect(controller.setPage).toHaveBeenCalledWith(page);
  }
});
