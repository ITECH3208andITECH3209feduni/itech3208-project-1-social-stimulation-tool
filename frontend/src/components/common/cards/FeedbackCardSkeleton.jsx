import { Avatar, Card, HStack, Skeleton, VStack } from "@chakra-ui/react";

function FeedbackCardSkeleton() {
    return (
        <Card.Root borderColor="gray.500">
            <Card.Body>
                <HStack gap={4}>
                    <Skeleton bg={"gray.400"} boxSize="40px" borderRadius="full" />

                    <VStack alignItems="start" gap={2}>
                        <Skeleton height="16px" width="100px" bg={"gray.400"}/>
                        <Skeleton height="14px" width="80px" bg={"gray.400"}/>
                    </VStack>
                </HStack>

                <VStack alignItems="start" mt={4} gap={2}>
                    <Skeleton height="14px" width="100%" bg={"gray.400"}/>
                    <Skeleton height="14px" width="90%" bg={"gray.400"}/>
                    <Skeleton height="14px" width="70%" bg={"gray.400"}/>
                </VStack>
            </Card.Body>
        </Card.Root>
    );
}

export default FeedbackCardSkeleton;
