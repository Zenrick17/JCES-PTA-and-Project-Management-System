<script>
    const openCreateProjectModal = document.getElementById('openCreateProjectModal');
    const closeCreateProjectModal = document.getElementById('closeCreateProjectModal');
    const cancelCreateProjectModal = document.getElementById('cancelCreateProjectModal');
    const createProjectModal = document.getElementById('createProjectModal');

    const openModal = () => {
        createProjectModal.classList.remove('hidden');
        createProjectModal.classList.add('flex');
    };

    const closeModal = () => {
        createProjectModal.classList.add('hidden');
        createProjectModal.classList.remove('flex');
    };

    openCreateProjectModal.addEventListener('click', openModal);
    closeCreateProjectModal.addEventListener('click', closeModal);
    cancelCreateProjectModal.addEventListener('click', closeModal);

    createProjectModal.addEventListener('click', (event) => {
        if (event.target === createProjectModal) {
            closeModal();
        }
    });
</script>
