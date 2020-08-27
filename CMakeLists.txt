########### Tutorial three ##############
if (CFITSIO_FOUND)
add_executable(pihqccd pihqccd.cpp)

target_link_libraries(pyhqccd indidriver)
endif (CFITSIO_FOUND)
