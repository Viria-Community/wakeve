<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Levée lorsqu'une transition de statut demandée à CompanyStatusService
 * ne correspond pas à l'état courant réel de la compagnie (ADR-005).
 * Type dédié plutôt qu'InvalidArgumentException générique, pour que les
 * Form Requests / contrôleurs puissent la catcher spécifiquement et
 * retourner une réponse HTTP 409/422 appropriée.
 */
class InvalidCompanyStatusTransitionException extends RuntimeException {}
