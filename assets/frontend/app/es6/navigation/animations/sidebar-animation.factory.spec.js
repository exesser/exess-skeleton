'use strict';

describe('Animation: Menu animation', function () {
  beforeEach(module('digitalWorkplaceApp'));

  let $timeout;

  let body;
  let sidebar;
  let sideBarLink;
  let sideBarTop;

  let sidebarAnimation;
  beforeEach(inject(function ($state, _sidebarAnimation_, _$timeout_) {
    mockHelpers.blockUIRouter($state);

    sidebarAnimation = _sidebarAnimation_;
    $timeout = _$timeout_;
  }));

  afterEach(function() {
    $('#animation').remove();
    body.removeClass('focus-is-open');
  });

  function makeTemplate(isSideBarOpen) {
    const sideBarExtraClass = isSideBarOpen ? 'is-open' : '';
    const mainExtraClass = isSideBarOpen ? 'is-inactive' : '';

    const template = `
      <div id="animation">
        <section class="main ${mainExtraClass}"></section>
        <nav class="sidebar ${sideBarExtraClass}">
          <div class="sidebarTop">Toppie</div>
          <ul>
            <li><a class="${sideBarExtraClass}"></a></li>
          </ul>
        </nav>
      </div>
    `;

    $('body').append(template);

    sidebar = $($('.sidebar')[0]);
    body = $($('body')[0]);
    sideBarLink = $($('.sidebar ul a')[0]);
    sideBarTop = $($('.sidebarTop'));
  }

  describe('sidebarAnimation.open', function() {
    it('should do an open animation when toggle is called and the menu is closed.', function() {
      makeTemplate(false);

      expect(body.hasClass('sidebar-is-open')).toBe(false);
      expect(sideBarLink.hasClass('is-visible')).toBe(false);

      const promise = sidebarAnimation.toggle();

      expect(body.hasClass('sidebar-is-open')).toBe(true);
      expect(sidebar.hasClass('is-open')).toBe(true);
      expect(sideBarLink.hasClass('is-visible')).toBe(true);

      let animationDone = false;
      promise.then(function() {
        expect(sideBarTop.css('text-indent')).toBe('0px');
        animationDone = true;
      });

      $timeout.flush(500); // sidebarAnimationTime
      expect(animationDone).toBe(true);
    });

    it('should not do an open animation when open is called and the menu is already open.', function(done) {
      makeTemplate(true);

      const promise = sidebarAnimation.open();

      expect(sidebar.hasClass('is-open')).toBe(true);

      promise.then(done);

      $timeout.flush(1); // sidebarAnimationTime
    });
  });

  describe('sidebarAnimation.close', function() {
    it('should do a close animation when toggle is called and the menu is open.', function(done) {
      makeTemplate(true);

      const promise = sidebarAnimation.toggle();

      expect(body.hasClass('sidebar-is-open')).toBe(false);
      expect(sidebar.hasClass('is-open')).toBe(false);
      expect(sideBarLink.hasClass('is-visible')).toBe(false);

      promise.then(done);

      $timeout.flush(500); // sidebarAnimationTime
    });

    it('should not do an close animation when close is called and the menu is already closed.', function(done) {
      makeTemplate(false);

      const promise = sidebarAnimation.close();

      expect(sidebar.hasClass('is-open')).toBe(false);

      promise.then(done);

      $timeout.flush(1); // sidebarAnimationTime
    });
  });
});
