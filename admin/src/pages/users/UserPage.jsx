import { Pagination, VStack, ButtonGroup, IconButton, Heading, Center } from "@chakra-ui/react";
import { LuChevronLeft, LuChevronRight } from "react-icons/lu";
import UserDetailDrawer from "@/components/common/drawers/UserDetailDrawer";
import UserDeleteDialog from "@/components/common/dialogs/UserDeleteDialog";
import UserGrid from "@/components/common/grids/UserGrid";
import UserSkeletonGrid from "@/components/common/grids/UserSkeletonGrid";
import useUser from "@/hooks/common/userUser";
import { useQueryClient } from "@tanstack/react-query";
import { userQueries } from "@/api/users/user.queries";
import useUpdateUser from "@/hooks/users/useUpdateUser";
import useDeleteUser from "@/hooks/users/useDeleteUser";

function UserPage() {
    const queryClient = useQueryClient();
    const { page, setPage, limit, users, pagination, isLoading } = useUser();
    
    const { 
        isOpen: isUpdateOpen, 
        onClose: closeUpdate, 
        handleUpdate,
        user: updateTargetUser
    } = useUpdateUser();
    
    const { 
        isOpen: isDeleteOpen, 
        onClose: closeDelete, 
        handleDelete,
        user: deleteTargetUser
    } = useDeleteUser();

    const handlePrefetchPage = (targetNumber) => {
        if (targetNumber < 1 || targetNumber > pagination.totalPages) return;

        queryClient.prefetchQuery(userQueries.list({ page, limit }));
    };

    return (
        <VStack gap={6} align="stretch">
            <Heading alignSelf={"start"} color="dark.500" fontSize={"30px"} fontWeight="bold">
                User Management
            </Heading>

            {isLoading ? <UserSkeletonGrid /> : <UserGrid users={users} />}

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

            {/* Open User Detail Drawer */}
            <UserDetailDrawer isOpen={isUpdateOpen} onClose={closeUpdate} onSave={handleUpdate} user={updateTargetUser} />

            {/* Open User Delete Dialog */}
            <UserDeleteDialog isOpen={isDeleteOpen} onClose={closeDelete} onSave={handleDelete} user={deleteTargetUser} />
        </VStack>
    );
}

export default UserPage;
