/**
 * smartPoll(fn, interval)
 * Calls fn immediately, then repeats every `interval` ms.
 * Pauses automatically when the browser tab is hidden,
 * and fires fn immediately when the tab becomes visible again.
 * This prevents unnecessary requests when the user isn't looking at the page.
 */
function smartPoll(fn, interval) {
    var timer = null;
    var missed = false;

    function schedule() {
        timer = setInterval(function () {
            if (document.hidden) {
                missed = true;
            } else {
                fn();
            }
        }, interval);
    }

    document.addEventListener('visibilitychange', function () {
        if (!document.hidden && missed) {
            missed = false;
            fn();
        }
    });

    fn();
    schedule();
}
