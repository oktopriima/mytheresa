<?php

namespace App\Contract;

use AllowDynamicProperties;
use App\Contract\Interfaces\ServiceInterface;
use App\Contract\Responses\ServiceResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\Logger;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AllowDynamicProperties]
abstract class AbstractServices implements ServiceInterface
{
    protected mixed $dto;

    public function __construct(
        DenormalizerInterface $denormalizer,
        ValidatorInterface    $validator,
    )
    {
        $this->denormalizer = $denormalizer;
        $this->validator = $validator;
    }

    protected static function success($result, string $message = 'true'): ServiceResponse
    {
        return (new ServiceResponse($result, $message, true))->setHttpCode(Response::HTTP_OK);
    }

    protected static function error($result, string $message = "error", bool $status = false): ServiceResponse
    {
        return (new ServiceResponse($result, $message, $status))->setHttpCode(Response::HTTP_BAD_REQUEST);
    }

    protected static function catchError(\Throwable $th, $result, string $message = "error"): ServiceResponse
    {
        $logger = new Logger();
        $logger->error('Cath an error', [
            'Message' => $th->getMessage(),
            'File' => $th->getFile(),
            'Line' => $th->getLine(),
        ]);

        return (new ServiceResponse($result, $message, false))->setHttpCode(Response::HTTP_BAD_REQUEST);
    }

    protected function denormalize(array $params, string $class): void
    {
        try {
            $this->dto = $this->denormalizer->denormalize($params, $class);
        } catch (\Throwable $th) {
            $logger = new Logger();
            $logger->error('Cath an error', [
                'Message' => $th->getMessage(),
                'File' => $th->getFile(),
                'Line' => $th->getLine(),
            ]);
        }
    }

    protected function validate(array $params, string $class): array
    {
        $this->denormalize($params, $class);
        $errors = $this->validator->validate($this->dto);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }

            return $errorMessages;
        }

        return [];
    }
}
