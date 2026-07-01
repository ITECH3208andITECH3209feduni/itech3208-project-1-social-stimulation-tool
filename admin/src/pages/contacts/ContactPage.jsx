import {
    IconButton,
    VStack,
    Pagination,
    ButtonGroup,
    Heading,
    Center,
    Spinner,
} from "@chakra-ui/react";
import { LuChevronLeft, LuChevronRight } from "react-icons/lu";
import ContactGrid from "@/components/common/grids/ContactGrid";
import { useQueryClient } from "@tanstack/react-query";
import { contactQueries } from "@/api/contacts/contact.queries";
import useContact from "@/hooks/common/useContact";
import ContactSkeletonGrid from "@/components/common/grids/ContactSkeletonGrid";

function ContactPage() {
    const queryClient = useQueryClient();
    const { page, setPage, limit, contacts, pagination, isLoading } = useContact();

    const handlePrefetchPage = (targetNumber) => {
        if (targetNumber < 1 || targetNumber > pagination.totalPages) return;

        queryClient.prefetchQuery(contactQueries.list({ page: targetNumber, limit }));
    };

    return (
        <VStack gap={6} align="stretch">
            <Heading alignSelf={"start"} color="dark.500" fontSize={"30px"} fontWeight="bold">
                Contact Management
            </Heading>

            {isLoading ? (
                <ContactSkeletonGrid count={6} />
            ) : (
                <ContactGrid contacts={contacts} />
            )}

            {/* Pagination */}
            {pagination.totalPages > 1 && (
                <Center mt={4}>
                    <Pagination.Root
                        count={pagination.total}
                        pageSize={pagination.limit}
                        page={page}
                        onPageChange={(e) => setPage(e.page)}
                    >
                        <ButtonGroup variant="ghost" size="sm" wrap="wrap">
                            <Pagination.PrevTrigger asChild>
                                <IconButton onMouseEnter={() => handlePrefetchPage(page - 1)}>
                                    <LuChevronLeft />
                                </IconButton>
                            </Pagination.PrevTrigger>

                            <Pagination.Items
                                render={(pageObj) => (
                                    <IconButton
                                        variant={pageObj.value === page ? "solid" : "ghost"}
                                        bg={pageObj.value === page ? "brand.500" : "transparent"}
                                        color={pageObj.value === page ? "white" : "inherit"}
                                        borderRadius={"full"}
                                        onMouseEnter={() => handlePrefetchPage(pageObj.value)}
                                    >
                                        {pageObj.value}
                                    </IconButton>
                                )}
                            />

                            <Pagination.NextTrigger asChild>
                                <IconButton onMouseEnter={() => handlePrefetchPage(page + 1)}>
                                    <LuChevronRight />
                                </IconButton>
                            </Pagination.NextTrigger>
                        </ButtonGroup>
                    </Pagination.Root>
                </Center>
            )}
        </VStack>
    );
}

export default ContactPage;
