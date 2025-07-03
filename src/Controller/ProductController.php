<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Form\ProductTypeForm;
use App\Repository\ProductRepository;
use App\Service\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{
    #[Route('admin/view/products', name: 'app_product')]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();
        return $this->render('admin/product/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('admin/add/products', name: 'app_product_add')]
    public function new(Request $request, EntityManagerInterface $entityManager, UploaderHelper $uploaderHelper): Response
    {
        $product = new Product();

        $form = $this->createForm(ProductTypeForm::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $product->setCreatedAt(new \DateTimeImmutable());

            $uploadedFile = $form->get('image')->getData();

            if ($uploadedFile) {
                $newFilename = $uploaderHelper->uploadProductImage($uploadedFile);
                $product->setImage($newFilename);
            }
            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', 'Product added!');

            return $this->redirectToRoute('app_product');
        }
        return $this->render('admin/product/new.html.twig', [
            'form' => $form->createView(),
            'isEdit' => false,
        ]);
    }

    #[Route('admin/product/{id}', name: 'app_product_show')]
    public function show(Product $product): Response
    {
        return $this->render('admin/product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('admin/add/products/edit/{id}', name: 'app_product_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, Product $product, UploaderHelper $uploaderHelper): Response
    {
        $originalThumbail = $product->getImage();

        $form = $this->createForm(ProductTypeForm::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $uploadedFile = $form['image']->getData();;

            if ($uploadedFile) {
                if ($originalThumbail) {
                    $uploadedFile->deleteProductImage($uploadedFile);
                }
                $newFilename = $uploaderHelper->uploadProductImage($uploadedFile);
                $product->setImage($newFilename);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Product updated!');

            return $this->redirectToRoute('app_product');
        }
        return $this->render('admin/product/new.html.twig', [
            'form' => $form,
            'isEdit' => true,
        ]);
    }

    #[Route('admin/add/products/delete/{id}', name: 'app_product_delete')]
    public function delete(Request $request, EntityManagerInterface $entityManager, Product $product, UploaderHelper $uploaderHelper): Response
    {
        $thumbnail = $product->getImage();

        if ($thumbnail) {
            $uploaderHelper->deleteProductImage($thumbnail);
        }
        $entityManager->remove($product);
        $entityManager->flush();
        $this->addFlash('success', 'Product deleted!');
        return $this->redirectToRoute('app_product');
    }
}
