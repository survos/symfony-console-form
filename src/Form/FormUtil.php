<?php

namespace Matthias\SymfonyConsoleForm\Form;

use Symfony\Component\Form\Exception\RuntimeException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;

class FormUtil
{
    /**
     * @param FormInterface $form
     *
     * @return array
     */
    public static function typeAncestry(FormInterface $form)
    {
        $types = [];
        self::typeAncestryForType($form->getConfig()->getType(), $types);

        return $types;
    }

    /**
     * @param ResolvedFormTypeInterface|null $formType
     * @param array                          $types
     */
    public static function typeAncestryForType(ResolvedFormTypeInterface $formType = null, array &$types)
    {
        if (!($formType instanceof ResolvedFormTypeInterface)) {
            return;
        }

        $types[] = get_class($formType->getInnerType());

        self::typeAncestryForType($formType->getParent(), $types);
    }

    /**
     * @param FormInterface $form
     * @param mixed         $type
     *
     * @return bool
     */
    public static function isTypeInAncestry(FormInterface $form, $type)
    {
        return in_array($type, self::typeAncestry($form));
    }

    /**
     * @param FormInterface $form
     *
     * @return string
     */
    public static function type(FormInterface $form)
    {
        return get_class($form->getConfig()->getType()->getInnerType());
    }

    /**
     * @param FormInterface $form
     *
     * @return string
     */
    public static function label(FormInterface $form)
    {
        $label = $form->getConfig()->getOption('label');

        if (!$label) {
            $label = self::humanize($form->getName());
        }

        return $label;
    }

    /**
     * @param FormInterface $form
     *
     * @return bool
     */
    public static function isCompound(FormInterface $form)
    {
        return $form->getConfig()->getCompound();
    }

    /**
     * @param FormTypeInterface|string $formType
     *
     * @return string
     */
    public static function formTypeToString($formType)
    {
        if ($formType instanceof FormTypeInterface) {
            return get_class($formType);
        }

        if (is_string($formType)) {
            return $formType;
        }

        throw new RuntimeException(
            sprintf(
                'Unable to determine form type for %s. Expected string or instance of %s',
                is_object($formType) ? get_class($formType) : gettype($formType),
                FormInterface::class
            )
        );
    }

    /**
     * Copied from Symfony\Component\Form method humanize.
     *
     * @param string $text
     *
     * @return string
     */
    private static function humanize($text)
    {
        return ucfirst(trim(strtolower(preg_replace(array('/([A-Z])/', '/[_\s]+/'), array('_$1', ' '), $text))));
    }
}
