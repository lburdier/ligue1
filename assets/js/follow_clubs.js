document.addEventListener('DOMContentLoaded', () => {
    // Get all follow checkboxes
    const followCheckboxes = document.querySelectorAll('.follow-checkbox');

    // Load follow status from localStorage
    followCheckboxes.forEach(checkbox => {
        const clubId = checkbox.dataset.clubId;
        const isFollowed = localStorage.getItem(`club_follow_${clubId}`) === 'true';
        checkbox.checked = isFollowed;

        // Add event listener to update localStorage on change
        checkbox.addEventListener('change', () => {
            localStorage.setItem(`club_follow_${clubId}`, checkbox.checked);
            console.log(`Club ${clubId} follow status: ${checkbox.checked}`);
        });
    });
});
