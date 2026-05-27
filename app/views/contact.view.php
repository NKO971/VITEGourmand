
<body>
    <main>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($success); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form id="formulaire" action="?page=contact" method="post">
            <fieldset id="">
                <h1 class="row justify-content-md-center">Formulaire de contact</h1>
                <div class="row justify-content-md-center">
                    <div class="col-4">
                        <div class="mb-3" id="nom-div">
                            <label for="nom" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="nom" name="nom" placeholder="Entrez votre nom" required>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3" id="prenom-div">
                            <label for="prenom" class="form-label">Prénom</label>
                            <input type="text" class="form-control" id="prenom" name="prenom" placeholder="Entrez votre prénom" required>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-md-center">
                    <div class="col-8">
                        <div class="mb-3" id="email-div">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Entrez votre email" required>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-md-center">
                    <div class="col-8">
                        <div class="mb-3" id="message-div">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" placeholder="Votre message" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="row mb-3 justify-content-center">
                    <div class="col-2">
                        <button class="btn btn-primary" type="submit">Envoyer</button>
                    </div>
                </div>
            </fieldset>
        </form>
    </main>

</body>

</html>
