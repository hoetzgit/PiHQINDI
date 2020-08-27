########### Tutorial three ##############
if (CFITSIO_FOUND)
add_executable(tutorial_three pihq.cpp)

target_link_libraries(indidriver)
endif (CFITSIO_FOUND)
