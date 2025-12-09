document.addEventListener("DOMContentLoaded", () => {
    const buttons = document.querySelectorAll(".tabButton");
    const sections = document.querySelectorAll(".tabContent");

    buttons.forEach(btn => {
        btn.addEventListener("click", () => {
            const target = btn.dataset.target;

            buttons.forEach(b => b.classList.remove("active"));
            btn.classList.add("active");

            sections.forEach(sec => {
                sec.classList.toggle("hidden", sec.id !== target);
            });
        });
    });

    const params = new URLSearchParams(window.location.search);
    const tab = params.get("tab");
    if (tab) {
        const btn = document.querySelector(`.tabButton[data-target="${tab}"]`);
        if (btn) btn.click();
    }
});
