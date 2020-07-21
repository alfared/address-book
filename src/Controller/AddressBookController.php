<?php

namespace App\Controller;

use App\Form\AddressBookType;
use App\Repository\AddressBookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\AddressBook;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Service\PaginationService;


class AddressBookController extends AbstractController
{
    /**
     * @var AddressBookRepository\
     */
    private $addressBookRepository;

    /**
     * AddressBookController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->addressBookRepository = $entityManager->getRepository(AddressBook::class);
    }

    /**
     * @Route("/", name="address_book")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function indexAction(Request $request, PaginationService $pagination): Response
    {
        $currentPage = $request->query->getInt('page') ?: 1;
        $query = $this->addressBookRepository->listAll();
        $results = $pagination->paginate($query, $currentPage, AddressBook::NUMBER_OF_ITEMS);

        return $this->render('addressBook/index.html.twig', [
            'addresses' => $results,
            'lastPage' => $pagination->lastPage($results),
            'total' => $pagination->total($results),
            'thisPage' => $currentPage,
            'maxPages' => $pagination->getMaxPages($results)

        ]);
    }

    /**
     * @Route("/add_address_book", name="add_address_book")
     */
    public function addAction(Request $request, FileUploader $fileUploader): Response
    {
        $addressBook = new AddressBook();
        $form = $this->createForm(AddressBookType::class, $addressBook);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $inputs = $form->getData();
            $this->addressBookRepository->store($inputs);
            $this->addFlash('success', 'Address Book created Successfully!');
            return $this->redirectToRoute('address_book');
        }

        return $this->render('addressBook/new.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit_address_book/{id}", name="edit_address_book")
     * @param $id
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */

    public function editAction(Request $request, $id): Response
    {
        $addressBook = $this->addressBookRepository->find($id);
        if (!$addressBook){
            throw $this->createNotFoundException(
                'No Address book found for id ' . $id
            );
        }
        $form = $this->createForm(AddressBookType::class, $addressBook);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $inputs = $form->getData();
            $this->addressBookRepository->store($inputs);
            $this->addFlash('success', 'Address Book updated Successfully');
            return $this->redirectToRoute('address_book');
        }
        return $this->render('addressBook/edit.html.twig',[
            'form' => $form->createView(),
            'id' => $id
        ]);
    }

    /**
     * @Route("/delete_address_book/{id}", name="delete_address_book")
     * @param $id
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteAction($id): Response
    {
         $addressBook  = $this->addressBookRepository->find($id);
         if (!$addressBook){
             throw $this->createNotFoundException(
                 'No Address book found for id ' . $id
             );
         }

         $this->addressBookRepository->remove($id);
         $this->addFlash('success', 'Item Deleted Successfully');
         return $this->redirectToRoute('address_book');
    }

}