document.addEventListener('DOMContentLoaded', function() {
    const dobInput = document.getElementById('dob');
    const ageSpan = document.getElementById('age');

    dobInput.addEventListener('change', function() {
        const dob = new Date(this.value);
        const age = new Date().getFullYear() - dob.getFullYear();
        ageSpan.textContent = `Age: ${age}`;
    });
});


    const candidatesDiv = document.getElementById('candidates');
    const candidateDiv = document.createElement('div');
    candidateDiv.className = 'candidate';
    candidateDiv.innerHTML = `
        <h3>Candidate ${candidateCount + 1}</h3>
        <label for="candidate-image-${candidateCount}">Profile Image</label>
        <input type="file" id="candidate-image-${candidateCount}" name="candidate-image-${candidateCount}" accept="image/*" required>
        
        <label for="candidate-name-${candidateCount}">Full Name</label>
        <input type="text" id="candidate-name-${candidateCount}" name="candidate-name-${candidateCount}" required>
        
        <label for="candidate-program-${candidateCount}">Program</label>
        <input type="text" id="candidate-program-${candidateCount}" name="candidate-program-${candidateCount}" required>
    `;
    candidatesDiv.appendChild(candidateDiv);
    candidateCount++;
}

document.addEventListener('DOMContentLoaded', () => {
    const themeToggleButton = document.getElementById('theme-toggle');
    const body = document.body;
    const header = document.querySelector('header');
    const footer = document.querySelector('footer');
    const sections = document.querySelectorAll('.about, .comment-section, .form-container');

    themeToggleButton.addEventListener('click', () => {
        body.classList.toggle('dark-theme');
        header.classList.toggle('dark-theme');
        footer.classList.toggle('dark-theme');
        sections.forEach(section => section.classList.toggle('dark-theme'));
    });
});