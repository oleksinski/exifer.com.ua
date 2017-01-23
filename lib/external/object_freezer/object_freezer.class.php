<?

Class Object_Freezer
{
    public static function freeze($object)
    {
        $state     = array();
        $reflector = new ReflectionObject($object);

        foreach ($reflector->getProperties() as $attribute) {
            $attribute->setAccessible(TRUE);
            $state[$attribute->getName()] =
            $attribute->getValue($object);
        }

        return array(
          'className' => get_class($object), 'state' => $state
        );
    }

    public static function thaw(array $frozenObject)
    {
        if (!class_exists($frozenObject['className'])) {
            throw new RuntimeException(
              sprintf(
                'Class "%s" could not be found.',
                $frozenObject['className']
              )
            );
        }

        // Use a "trick" to create an object of the class
        // without calling its constructor. After all, we
        // are not creating a new object but are merely
        // thawing a previously created and currently
        // frozen one.
        $object = unserialize(
          sprintf(
            'O:%d:"%s":0:{}',
            _strlen($frozenObject['className']),
            $frozenObject['className']
          )
        );

        $reflector = new ReflectionObject($object);

        foreach ($frozenObject['state'] as $name => $value) {
            $attribute = $reflector->getProperty($name);
            $attribute->setAccessible(TRUE);
            $attribute->setValue($object, $value);
        }

        return $object;
    }
}