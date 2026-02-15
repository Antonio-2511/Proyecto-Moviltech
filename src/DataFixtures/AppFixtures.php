<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Categoria;
use App\Entity\Producto;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {


        $admin = new User();
        $admin->setEmail('admin@moviltech.com');
        $admin->setNombre('Admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword(
            $this->passwordHasher->hashPassword($admin, 'password')
        );
        $manager->persist($admin);

        $user = new User();
        $user->setEmail('user@moviltech.com');
        $user->setNombre('Usuario');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, 'password')
        );
        $manager->persist($user);



        $moviles = new Categoria();
        $moviles->setNombre('Móviles');
        $moviles->setDescripcion('Smartphones de última generación');
        $manager->persist($moviles);

        $accesorios = new Categoria();
        $accesorios->setNombre('Accesorios');
        $accesorios->setDescripcion('Complementos y accesorios para tus dispositivos');
        $manager->persist($accesorios);

        $tablets = new Categoria();
        $tablets->setNombre('Tablets');
        $tablets->setDescripcion('Tablets para trabajo, estudio y ocio');
        $manager->persist($tablets);



        $productos = [

            // Móviles
            [
                'nombre' => 'iPhone 15 Pro 128GB',
                'descripcion' => 'Smartphone Apple con chip A17 Pro, pantalla OLED Super Retina XDR de 6,1 pulgadas y sistema de cámaras profesional.',
                'precio' => 1199,
                'stock' => 15,
                'categoria' => $moviles
            ],
            [
                'nombre' => 'Samsung Galaxy S24 256GB',
                'descripcion' => 'Teléfono Android con pantalla AMOLED 6,2", procesador de alto rendimiento y triple cámara con inteligencia artificial.',
                'precio' => 949,
                'stock' => 20,
                'categoria' => $moviles
            ],
            [
                'nombre' => 'Xiaomi 13T 256GB',
                'descripcion' => 'Smartphone con pantalla AMOLED 144Hz, gran autonomía y cámara Leica para fotografía avanzada.',
                'precio' => 599,
                'stock' => 25,
                'categoria' => $moviles
            ],

            // Tablets
            [
                'nombre' => 'iPad Air 10.9" 64GB',
                'descripcion' => 'Tablet ligera y potente con chip M1, ideal para productividad y entretenimiento.',
                'precio' => 699,
                'stock' => 12,
                'categoria' => $tablets
            ],
            [
                'nombre' => 'Samsung Galaxy Tab S9',
                'descripcion' => 'Tablet premium con pantalla AMOLED 11", S-Pen incluido y gran rendimiento multitarea.',
                'precio' => 799,
                'stock' => 10,
                'categoria' => $tablets
            ],

            // Accesorios
            [
                'nombre' => 'Auriculares Bluetooth Sony WH-1000XM5',
                'descripcion' => 'Auriculares inalámbricos con cancelación de ruido activa y sonido de alta fidelidad.',
                'precio' => 399,
                'stock' => 30,
                'categoria' => $accesorios
            ],
            [
                'nombre' => 'Cargador rápido USB-C 30W',
                'descripcion' => 'Cargador compacto compatible con smartphones y tablets con carga rápida.',
                'precio' => 29,
                'stock' => 50,
                'categoria' => $accesorios
            ],
            [
                'nombre' => 'Funda protectora transparente iPhone',
                'descripcion' => 'Funda de silicona resistente a golpes compatible con modelos recientes de iPhone.',
                'precio' => 19,
                'stock' => 40,
                'categoria' => $accesorios
            ],
        ];

        foreach ($productos as $data) {
            $producto = new Producto();
            $producto->setNombre($data['nombre']);
            $producto->setDescripcion($data['descripcion']);
            $producto->setPrecio($data['precio']);
            $producto->setStock($data['stock']);
            $producto->setCategoria($data['categoria']);

            $manager->persist($producto);
        }

        $manager->flush();
    }
}
